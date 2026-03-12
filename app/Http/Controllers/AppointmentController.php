<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use App\Models\Nationality;  
use App\Models\User;


use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentsExport;
    use App\Events\AppointmentCalled;
use App\Models\AppointmentTracking;
use Illuminate\Support\Facades\Mail;
use App\Models\DocumentCollection;

use App\Mail\DocumentCollectionReadyMail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;




class AppointmentController extends Controller
{
    public function index()
{
    $today = Carbon::today();

    /**
     * ============================
     * ACTIVE APPOINTMENTS (UNCHANGED)
     * ============================
     */
    $activeAppointments = Appointment::whereDate('appointment_date', $today)
        ->where(function ($q) {
            $q->where('status', 'waiting')
              ->orWhereNotNull('window_no');
        })
        ->whereNotIn('status', ['accepted', 'pending', 'cancelled'])
        ->orderByRaw("
            CASE
                WHEN window_no IS NOT NULL THEN 1
                ELSE 2
            END
        ")
        ->orderBy('appointment_start_time', 'asc')
        ->get();

    /**
     * ============================
     * COMPLETED TODAY (UNCHANGED)
     * ============================
     */
    $completedToday = Appointment::whereDate('appointment_date', $today)
        ->where('status', 'accepted')
        ->whereDate('updated_at', $today)
        ->orderBy('updated_at', 'asc')
        ->get();

    /**
     * ============================
     * MERGE (completed at bottom)
     * ============================
     */
    $appointments = $activeAppointments->concat($completedToday);

    /**
     * ============================
     * USED WINDOWS (UNCHANGED)
     * ============================
     */
    $usedWindows = Appointment::whereDate('appointment_date', $today)
        ->whereNotNull('window_no')
        ->whereNotIn('status', ['accepted', 'pending'])
        ->pluck('window_no')
        ->toArray();

    /**
     * ============================
     * NORMAL USERS (NEW)
     * ============================
     */
    $users = User::where('user_type', User::TYPE_NORMAL)
        ->orderBy('name')
        ->get(['id', 'name', 'user_type']);

    return view('appointments.index', compact(
        'appointments',
        'usedWindows',
        'users'
    ));
}
public function bulkReadyMailPage()
{
    $appointments = Appointment::where('status', Appointment::STATUS_PROCESS_COMPLETED)
        ->whereNull('delivery_status')
        ->latest()
        ->paginate(50);   // ✅ use paginate instead of get

    return view('appointments.bulk-ready-mail', compact('appointments'));
}


public function scanReadyMail(Request $request)
{
    $request->validate([
        'barcode' => 'required|string'
    ]);

    $appointment = Appointment::where('appointment_no', $request->barcode)
        ->where('status', Appointment::STATUS_PROCESS_COMPLETED)
        ->first();

    if (!$appointment) {
        return response()->json([
            'error' => 'Application not found or not process completed.'
        ], 404);
    }

    return response()->json([
        'id' => $appointment->id,
        'reference' => $appointment->ReferenceNr,
        'name' => $appointment->applicant_name,
        'service' => $appointment->service
    ]);
}

public function collectDocumentsPage()
{
    $appointments = Appointment::where('delivery_status', Appointment::STATUS_EMAILSENT)
        ->latest()
        ->paginate(20);

    return view('appointments.collect-documents', compact('appointments'));
}


public function scanCollectDocument(Request $request)
{
    $request->validate([
        'barcode' => 'required|string'
    ]);

    $appointment = Appointment::where('appointment_no', $request->barcode)
        ->where('delivery_status', Appointment::STATUS_EMAILSENT)
        ->first();

    if (!$appointment) {
        return response()->json([
            'error' => 'Application not eligible for collection.'
        ]);
    }

    return response()->json([
        'id' => $appointment->id,
        'appointment_no' => $appointment->appointment_no,
        'reference' => $appointment->ReferenceNr,
        'name' => $appointment->applicant_name,
        'service' => $appointment->service
    ]);
}



public function processCollectDocument(Request $request)
{
    $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
        'remarks'        => 'nullable|string|max:500'
    ]);

    $appointment = Appointment::findOrFail($request->appointment_id);

    DB::transaction(function () use ($appointment, $request) {

        $appointment->update([
            'delivery_status' => Appointment::STATUS_COLLECTED
        ]);

        // Prepare remarks
        $remarks = 'Documents collected';

        if ($request->remarks) {
            $remarks .= ' | ' . $request->remarks;
        }

        // Track action
        $appointment->track(
            AppointmentTracking::ACTION_DELIVERY_UPDATED,
            auth()->id(),
            $remarks
        );
    });

    return response()->json(['success' => true]);
}





public function processBulkReadyMail(Request $request)
{
    $request->validate([
        'appointment_ids' => 'required|array|min:1',
        'appointment_ids.*' => 'exists:appointments,id',
        'collection_date' => 'required|date',
        'collection_time' => 'required'
    ]);

    DB::transaction(function () use ($request) {

        $appointments = Appointment::whereIn('id', $request->appointment_ids)
            ->where('status', Appointment::STATUS_PROCESS_COMPLETED)
            ->get();

        foreach ($appointments as $appointment) {

    $appointment->update([
        'delivery_status' => Appointment::STATUS_MAIL
    ]);

    DocumentCollection::updateOrCreate(
    ['appointment_no' => $appointment->appointment_no],
    [
        'collection_date' => $request->collection_date,
        'collection_time' => $request->collection_time,
        'passport_no' => $appointment->passportno ?? 'N/A'
    ]
);



    $appointment->track(
        AppointmentTracking::ACTION_DELIVERY_UPDATED,
        auth()->id(),
        'Delivery status set to Ready to Mail via barcode scanning'
    );
}

    });

    return back()->with(
        'success',
        count($request->appointment_ids) . ' application(s) marked Ready to Mail.'
    );
}
 /* =========================================================
     | SUPERVISOR WORKFLOW
     |========================================================= */

public function supervisorCases(Request $request)
{
    abort_unless(auth()->user()->isSupervisor(), 403);

    $appointments = Appointment::with([
            'trackings',
            'trackings.assignedBy',
            'trackings.assignedTo',
        ])
        ->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('appointment_no', 'like', "%{$request->search}%")
                    ->orWhere('ReferenceNr', 'like', "%{$request->search}%")
                    ->orWhere('applicant_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        })
        ->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->whereIn('status', [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_CANCELLED,
            Appointment::STATUS_ACCEPTED,
        ])
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('appointments.supervisor-cases', compact('appointments'));
}







 /* =========================================================
     | USER / SUPERVISOR WORKFLOW
     |========================================================= */
   public function returnFromUser(Request $request)
{
    // -----------------------------
    // Load data
    // -----------------------------
    $appointment = Appointment::findOrFail($request->appointment_id);
    $user = auth()->user();

    // -----------------------------
    // Authorization (Normal Users Only)
    // -----------------------------
    if ($user->isNormal()) {

        // Must own the appointment
        abort_unless($appointment->current_assignee_id === $user->id, 403);

        // 🚫 BLOCK RETURN IF UNDER PROCESS OR PENDING
        // 🚫 Block ONLY return to pool when under process or pending
if (
    in_array($appointment->status, [
        Appointment::STATUS_PROCESS_IN_PROGRESS,
        Appointment::STATUS_PENDING,
    ])
    && $request->return_to === 'pool'
) {
    return back()->with('error', 'This application cannot be returned to main pool at this stage. You may forward it to Consular Attache instead.');
}

    }

DB::transaction(function () use ($appointment, $request, $user) {

    if ($user->isNormal()) {

        $request->validate([
    'return_to' => 'required|in:pool,supervisor',
    'remarks'   => 'required_if:return_to,supervisor|string|min:5|max:1000',
]);

// 🔒 Enforce NEW remark when forwarding to supervisor
if ($request->return_to === 'supervisor') {

    $latestTracking = $appointment->trackings()->latest()->first();
    $previousRemark = optional($latestTracking)->remarks;

    if ($previousRemark && trim($request->remarks) === trim($previousRemark)) {
        return back()->withErrors([
            'remarks' => 'You must enter the reason for forwarding.'
        ]);
    }
}

        $assignedTo = null;
        $action     = 'returned_to_pool';

        if ($request->return_to === 'supervisor') {

            $supervisor = User::where('user_type', User::TYPE_SUPERVISOR)->first();
            abort_if(!$supervisor, 500);

            $assignedTo = $supervisor->id;
            $action     = 'returned_to_supervisor';
        }

        $remarks = $request->remarks;
    }

    else {

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $assignedTo = $request->user_id;
        $action     = 'returned_by_supervisor';
        $remarks    = $request->remarks;
    }

    // ✅ DO NOT TOUCH WORKFLOW STATUS
   // ==============================
// UPDATE STATUS + ASSIGNEE
// ==============================

if ($user->isNormal()) {

    if ($request->return_to === 'supervisor') {

        // 🔵 Forward to Consular Attaché
        $appointment->update([
            'status' => Appointment::STATUS_SENT_TO_ATTACHE,
            'current_assignee_id' => $assignedTo,
        ]);

    } else {

        // 🟡 Return to pool
        $appointment->update([
            'status' => Appointment::STATUS_ASSIGNED,
            'current_assignee_id' => null,
        ]);
    }

} else {

    // 🔁 Supervisor sending back to officer
    $appointment->update([
        'status' => Appointment::STATUS_ASSIGNED,
        'current_assignee_id' => $assignedTo,
    ]);
}

   $appointment->track(
    $action,
    $assignedTo,
    $remarks
);
});

    return back();
}


    public function markPending(Request $request, Appointment $appointment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $appointment->status = Appointment::STATUS_PENDING;
        $appointment->reason_pending = $request->reason;
        $appointment->window_no = null;
        $appointment->returned_from_window = null;
        $appointment->save();

        return redirect()->back();
    }

    public function getAnnouncements()
    {
        $announcement = DB::table('announcements')->first();

        return response()->json([
            'messages' => $announcement
                ? json_decode($announcement->messages, true)
                : []
        ]);
    }

    public function indexexcel()
    {
        return view('appointments.exceldownload');
    }

    public function export(Request $request)
    {
        return Excel::download(
            new AppointmentsExport($request->from, $request->to),
            'appointments.xlsx'
        );
    }

    public function skip(Appointment $appointment)
    {
        if (strtolower($appointment->status) === 'accepted') {
            return back();
        }

        if (!$appointment->window_no) {
            return back();
        }

        $appointment->update([
            'status'    => 'waiting',
            'window_no' => null,
        ]);

        return back();
    }

    private function nullify($value)
    {
        return ($value === 'NULL' || $value === '' || is_null($value))
            ? null
            : trim($value);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        fgetcsv($handle, 0, ',');

        $errorRows  = [];
        $normalRows = [];
        $line = 1;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $line++;

            if (count($row) < 17) {
                $errorRows[] = [
                    'line'   => $line,
                    'data'   => $row,
                    'errors' => ['Invalid column count'],
                ];
                continue;
            }

            $errors = [];

            if (empty(trim($row[1]))) {
                $errors[] = 'Missing appointment number';
            }

            if (empty($row[3]) || !Carbon::hasFormat(trim($row[3]), 'd-m-Y')) {
                $errors[] = 'Invalid appointment date';
            }

            if (!empty($row[14]) && !Carbon::hasFormat(trim($row[15]), 'd-m-Y')) {
                $errors[] = 'Invalid date of birth';
            }

            $entry = [
                'line'   => $line,
                'data'   => $row,
                'errors' => $errors,
            ];

            empty($errors) ? $normalRows[] = $entry : $errorRows[] = $entry;

            if (count($errorRows) >= 5) {
                break;
            }
        }

        fclose($handle);

        $rows = !empty($errorRows)
            ? array_slice($errorRows, 0, 5)
            : array_slice($normalRows, 0, 5);

        return response()->json($rows);
    }

    public function uploadWithProgress(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        session([
            'csv_progress' => 0,
            'csv_total'    => 0,
            'csv_done'     => false,
            'csv_error'    => null,
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        fgetcsv($handle, 0, ',');

        $rows = [];
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        session(['csv_total' => count($rows)]);

        DB::beginTransaction();

try {

    // Delete today and future appointments
   Appointment::whereDate('appointment_date', '>=', Carbon::today())
    ->whereIn('status', ['waiting', 'cancelled'])
    ->delete();

    foreach ($rows as $index => $row) {
        session(['csv_progress' => $index + 1]);

        $appointmentNo = trim($row[1] ?? '');
        if ($appointmentNo === '') continue;

        if (Appointment::where('appointment_no', $appointmentNo)->exists()) continue;

        Appointment::create([
          'appointment_no'         => $appointmentNo,
            'service'                => trim($row[2]),
            'appointment_date'       => Carbon::createFromFormat('d-m-Y', $row[3])->format('Y-m-d'),
           'appointment_start_time' => $this->nullify($row[4]),
            'appointment_end_time'   => $this->nullify($row[5]),
            'passportno'             => $this->nullify($row[6]),
            'window_no'              => is_numeric($row[7]) ? (int) $row[7] : null,
            'processed_by'           => is_numeric($row[8]) ? (int) $row[8] : null,
            'applicant_name'         => trim($row[9]),
            'ReferenceNr'            => $this->nullify($row[10]),
            'gender'                 => $this->nullify($row[11]),
            'nationality'            => $this->nullify($row[12]),
            'mobile_number'          => $this->nullify($row[13]),
            'email'                  => $this->nullify($row[14]),
            'date_of_birth'          => !empty($row[15])
                ? Carbon::createFromFormat('d-m-Y', $row[15])->format('Y-m-d')
                : null,
            'address'                => $this->nullify($row[16]),
          //   'passportno'                => $this->nullify($row[16]),
            'status'                 => in_array(strtolower(trim($row[17])), ['approved'])
                ? 'waiting'
                : strtolower(trim($row[17] ?? 'waiting')),

        ]);

        usleep(30000);
    }

    DB::commit();
    session(['csv_done' => true]);

    return response()->json(['success' => true]);


            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            DB::rollBack();

            session([
                'csv_error' => $e->getMessage(),
                'csv_done'  => true,
            ]);

            return response()->json(['error' => true], 500);
        }
    }

    public function progress()
    {
        return response()->json([
            'current' => session('csv_progress', 0),
            'total'   => session('csv_total', 0),
            'done'    => session('csv_done', false),
            'error'   => session('csv_error', null),
        ]);
    }



public function assignWindow(Request $request, Appointment $appointment)
{
    $request->validate([
        'window_no' => 'required|integer|min:1|max:4',
    ]);

    $requestedWindow = (int) $request->window_no;

    // 🔒 Returned lock
    if (
        $appointment->returned_from_window !== null &&
        (int)$appointment->returned_from_window !== $requestedWindow
    ) {
        abort(403);
    }

    $appointment->update([
        'window_no' => $requestedWindow,
        'status' => 'assigned',
        'returned_from_window' => null,
    ]);

    // 🔔 RESTORE PUSHER BROADCAST
    broadcast(new AppointmentCalled(
        reference: $appointment->ReferenceNr,
        window: $requestedWindow
    ));

    return back();
}


    public function returnToQueue(Appointment $appointment)
    {
        if (!$appointment->window_no) return back();

        $appointment->returned_from_window = $appointment->window_no;
        $appointment->window_no = null;
        $appointment->status = 'waiting';
        $appointment->save();

        return back();
    }





public function complete(Appointment $appointment)
{
    $appointment->update([
        'status'    => Appointment::STATUS_ACCEPTED,
        'window_no' => null,
    ]);

    // Log tracking entry
    $appointment->trackings()->create([
        'assigned_by' => Auth::id(),
        'action'      => 'accepted_at_counter',
        'remarks'     => 'Accepted at the counter by: ' . Auth::user()->name,
    ]);

    return back();
}






    

public function assignUser(Request $request, Appointment $appointment)
{
    $request->validate([
        'current_assignee_id' => 'required|exists:users,id',
    ]);

    if (strtolower($appointment->status) !== 'accepted') {
        return back()->with('error', 'Only accepted applications can be assigned.');
    }

    DB::transaction(function () use ($appointment, $request) {

        $user = User::find($request->current_assignee_id);

        $appointment->update([
            'current_assignee_id' => $user->id,
            'status'              => Appointment::STATUS_ASSIGNED,
        ]);

        $appointment->track(
            AppointmentTracking::ACTION_ASSIGNED,
            $user->id,
            'Application assigned to ' . $user->name
        );
    });

    return back()->with('success', 'Application assigned successfully.');
}



public function updateStatus(Request $request, Appointment $appointment)
{
    $request->validate([
        'status'  => 'required|string',
        'remarks' => $request->status === Appointment::STATUS_PENDING
            ? 'required|string|min:3'
            : 'nullable|string',
    ]);

    DB::transaction(function () use ($request, $appointment) {

        $oldStatus = $appointment->status;
        $newStatus = $request->status;

        // ✅ Only proceed if status actually changes
        if ($oldStatus !== $newStatus) {

            // 1️⃣ Update appointment status
            $appointment->update([
                'status' => $newStatus,
            ]);



            
            // 2️⃣ If sent to Consular Attaché → assign to Supervisor
            if ($newStatus === Appointment::STATUS_SENT_TO_ATTACHE) {

                $supervisor = \App\Models\User::where(
                    'user_type',
                    \App\Models\User::TYPE_SUPERVISOR
                )->first();

                if ($supervisor) {
                    $appointment->update([
                        'current_assignee_id' => $supervisor->id,
                    ]);
                }
            }

            // 3️⃣ Prepare tracking remarks
            $remarksParts = [];

            if (!empty($request->remarks)) {
                $remarksParts[] = trim($request->remarks);
            }

            $oldLabel = Appointment::STATUS_LABELS[$oldStatus] ?? $oldStatus;
            $newLabel = Appointment::STATUS_LABELS[$newStatus] ?? $newStatus;

          $remarksParts[] = "Status changed from {$oldLabel} to {$newLabel}";

// 4️⃣ Create tracking record
$appointment->track(
    AppointmentTracking::ACTION_STATUS_UPDATED,
    $appointment->current_assignee_id,
    implode(' | ', $remarksParts)
);
        }
    });

    return back()->with('success', 'Application status updated successfully.');
}

public function editService(Appointment $appointment)
{
    $nationalities = Nationality::orderBy('Nationname')->get();

    return view('appointments.edit-service', compact('appointment', 'nationalities'));
}

public function updateService(Request $request, Appointment $appointment)
    {
        // 🔒 Prevent editing completed appointments
        if ($appointment->status === 'completed') {
            return back()->with('error', 'Completed appointments cannot be edited.');
        }

        // ✅ Validate input
       $validated = $request->validate([
    'applicant_name' => 'required|string|max:255',
    'service'        => 'required|string|max:255',
    'passportno'     => 'nullable|string|max:45',
    'split_service'  => 'nullable|boolean',
    'ReferenceNr'    => 'nullable|string|max:255',

    // ✅ allow null safely
    'child_name'       => 'nullable|string|max:255',
    'child_passportno' => 'nullable|string|max:45',
]);

if ($request->boolean('split_service') && empty(trim($request->child_name))) {
        return back()
            ->withErrors(['child_name' => 'Child name is required when splitting.'])
            ->withInput();
    }
        // Normalize incoming values early
        $newService = $validated['service'];
        $newName = trim($validated['applicant_name']);
        $newPassportNorm = isset($validated['passportno']) && $validated['passportno'] !== null
            ? trim($validated['passportno'])
            : null;

        $splitRequested = ($request->input('split_service') == 1);

        // ✅ Split only for this specific service
        $splitTriggerService = 'Passport - Birth Registration & Fresh Passport to New Born';

        DB::beginTransaction();

try {

    if ($newService === $splitTriggerService && $splitRequested) {

        $appointment->update([
            'applicant_name' => $newName,
            'service'        => 'Passport-Fresh Passport for New Born',
            'passportno'     => $newPassportNorm,
             'ReferenceNr'    => $request->ReferenceNr,
        ]);

        $appointment->track(
            'appointment_split',
            $appointment->current_assignee_id,
            "Split service from '{$splitTriggerService}' into Birth Registration + Fresh Passport for New Born."
        );

        $newAppointment = $appointment->replicate();

        $newAppointment->service = 'Misc-Birth Registration';
        $newAppointment->appointment_no = $this->generateApplicationNumber($appointment->appointment_no);
        $newAppointment->ReferenceNr = $this->generateSplitReferenceNr($appointment->ReferenceNr);
        $newAppointment->applicant_name = trim($validated['child_name']);
        $newAppointment->passportno = !empty($validated['child_passportno'])
            ? trim($validated['child_passportno'])
            : null;

        $newAppointment->save();

        $newAppointment->track(
            'appointment_created_from_split',
            $newAppointment->current_assignee_id,
            "Created from split of appointment {$appointment->id}"
        );

        DB::commit();

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Service successfully split into two applications.');
    }

    // NORMAL UPDATE FLOW
$changes = [];

if ($appointment->applicant_name !== $newName) {
    $changes[] = "Applicant name changed from '{$appointment->applicant_name}' to '{$newName}'";
}

if ($appointment->service !== $newService) {
    $changes[] = "Service changed from '{$appointment->service}' to '{$newService}'";
}

$oldPassportNorm = $appointment->passportno ? trim($appointment->passportno) : null;

if ($oldPassportNorm !== $newPassportNorm) {
    $changes[] = "Passport number updated from '" .
        ($oldPassportNorm ?? 'NULL') .
        "' to '" .
        ($newPassportNorm ?? 'NULL') .
        "'";
}
    if (!empty($changes)) {

        $appointment->update([
            'applicant_name' => $newName,
            'service'        => $newService,
            'passportno'     => $newPassportNorm,
        ]);

        $appointment->track(
            'appointment_updated',
            $appointment->current_assignee_id,
            implode(' | ', $changes)
        );
    }

    DB::commit();

    return redirect()
        ->route('appointments.index')
        ->with('success', 'Appointment updated successfully.');

} catch (\Throwable $e) {

    DB::rollBack();

    Log::error('updateService failed', [
        'error' => $e->getMessage()
    ]);

    return back()->with('error', 'Something went wrong while updating the appointment.');
}
    }

    /**
     * Generate a new application number.
     * Adjust this to your own format if needed.
     */
private function generateApplicationNumber(string $originalNumber): string
{
    // Generate random digit 0–9
    $randomDigit = random_int(0, 9);

    // Append to original number
    return $originalNumber . $randomDigit;
}
private function generateSplitReferenceNr(string $original): string
{
    $yy = now()->format('y');            // e.g. "26"
    $rest = substr($original, 3);        // drop first 3 chars (e.g. "202609007493")

    $base = "{$yy}-{$rest}";
    $ref = $base;

    // Ensure unique (in case you split same original more than once)
    $i = 0;
    while (\App\Models\Appointment::where('ReferenceNr', $ref)->exists()) {
        $i++;
        $ref = "{$base}-{$i}";           // 26-202609007493-1, -2, ...
    }

    return $ref;
}

public function updateDeliveryStatus(Request $request, Appointment $appointment)
{
    Log::info('Delivery status update request', [
        'appointment_no'  => $appointment->appointment_no,
        'previous_status' => $appointment->delivery_status,
        'new_status'      => $request->delivery_status,
    ]);

    // 🔐 Guard: delivery only after completion
    if (!$appointment->canHaveDeliveryStatus()) {
        return back()->withErrors(
            'Delivery status cannot be updated at this stage.'
        );
    }

    // ✅ Validate input
    $request->validate([
        'delivery_status' => 'required|in:' . implode(',', [
            Appointment::STATUS_MAIL,
            Appointment::STATUS_EMAILSENT,
            Appointment::STATUS_COLLECTED,
            Appointment::STATUS_DISPATCHED,
        ]),
        'collection_date' => 'required_if:delivery_status,' . Appointment::STATUS_MAIL,
        'collection_time' => 'required_if:delivery_status,' . Appointment::STATUS_MAIL,
        'passport_no'     => 'required_if:delivery_status,' . Appointment::STATUS_MAIL,
        'remarks'         => 'nullable|string|max:500',
    ]);

    /**
     * 🚫 BLOCK: EMAIL_SENT before READY_TO_MAIL
     */
    if ($request->delivery_status === Appointment::STATUS_EMAILSENT) {

        if ($appointment->delivery_status !== Appointment::STATUS_MAIL) {
            return back()->withErrors(
                'You must complete Ready to Mail before sending email.'
            );
        }

        if (!$appointment->documentCollection) {
            return back()->withErrors(
                'Collection details are missing. Please complete Ready to Mail first.'
            );
        }
    }

    try {

        DB::transaction(function () use ($request, $appointment) {

            $previousDeliveryStatus = $appointment->delivery_status;
            $newDeliveryStatus      = $request->delivery_status;

            /**
             * =========================
             * UPDATE DELIVERY STATUS
             * =========================
             */
            $appointment->update([
                'delivery_status' => $newDeliveryStatus,
            ]);

            /**
             * =========================
             * READY TO MAIL → SAVE COLLECTION DETAILS
             * =========================
             */
            if ($newDeliveryStatus === Appointment::STATUS_MAIL) {

                DocumentCollection::updateOrCreate(
                    ['appointment_no' => $appointment->appointment_no],
                    [
                        'collection_date' => $request->collection_date,
                        'collection_time' => $request->collection_time,
                        'passport_no'     => $request->passport_no,
                    ]
                );
            }

            /**
             * =========================
             * EMAIL SENT → SEND EMAIL
             * =========================
             */
            if (
                $newDeliveryStatus === Appointment::STATUS_EMAILSENT &&
                $previousDeliveryStatus !== Appointment::STATUS_EMAILSENT
            ) {

                Log::info('Attempting to send email', [
                    'appointment_no' => $appointment->appointment_no,
                    'email'          => $appointment->email,
                ]);

                $documentCollection = $appointment->documentCollection;

                if ($documentCollection && is_null($documentCollection->email_sent_at)) {

                    try {

                        $email = $request->email ?? $appointment->email;

// Save corrected email if changed
if ($request->filled('email') && $request->email !== $appointment->email) {

    Log::info('Email corrected before sending', [
        'appointment_no' => $appointment->appointment_no,
        'old_email' => $appointment->email,
        'new_email' => $request->email,
        'admin_id' => auth()->id(),
    ]);

    $appointment->update([
        'email' => $request->email
    ]);
}

Mail::to($email)
    ->send(new DocumentCollectionReadyMail($appointment));
                        $documentCollection->update([
                            'email_sent_at' => now(),
                        ]);

                    } catch (\Exception $e) {

                        Log::error('Email failed', [
                            'error' => $e->getMessage(),
                            'appointment_no' => $appointment->appointment_no,
                        ]);

                        throw $e;
                    }
                }
            }

            /**
             * =========================
             * TRACK DELIVERY CHANGE
             * =========================
             */
            $label = Appointment::DELIVERY_LABELS[$newDeliveryStatus] ?? $newDeliveryStatus;

            $remarks = "Delivery status changed to {$label}";

            // Special handling for dispatch
            if ($newDeliveryStatus === Appointment::STATUS_DISPATCHED) {

                $remarks = "Application dispatched"
                    . " | App No: {$appointment->appointment_no}"
                    . " | Ref No: {$appointment->ReferenceNr}";
            }

            // Append user remarks if provided
            if ($request->remarks) {
                $remarks .= " | " . $request->remarks;
            }

            $appointment->track(
                AppointmentTracking::ACTION_DELIVERY_UPDATED,
                $appointment->current_assignee_id,
                $remarks
            );
        });

    } catch (\Exception $e) {

    if ($request->expectsJson()) {
        return response()->json([
            'success' => false,
            'message' => 'Email sending failed'
        ], 500);
    }

    return back()->withErrors('Email sending failed.');
}

if ($request->expectsJson()) {

    return response()->json([
        'success' => true,
        'delivery_status' => $appointment->delivery_status,
        'delivery_status_label' =>
            Appointment::DELIVERY_LABELS[$appointment->delivery_status]
            ?? $appointment->delivery_status,
        'delivery_status_color' =>
            Appointment::DELIVERY_COLORS[$appointment->delivery_status]
            ?? 'secondary'
    ]);
}

    return back()->with('success', 'Delivery status updated successfully.');
}

    public function showDeleteForm()
{
    if (!auth()->user()->isSupervisor()) {
        abort(403, 'Unauthorized action.');
    }

    return view('admin.appointments.delete');
}






public function deleteAppointments(Request $request)
{
    if (!Gate::allows('delete-appointments')) {
        return redirect()
            ->route('appointments.assign.index')
            ->with('error', 'You are not authorized to delete appointments.');
    }

    $allowedStatuses = [
        Appointment::STATUS_PROCESS_COMPLETED,
        Appointment::STATUS_CANCELLED,
        Appointment::STATUS_ACCEPTED,
    ];

    $request->validate([
        'delete_type'     => 'required|in:appointment_no,date_range',
        'appointment_no'  => 'required_if:delete_type,appointment_no',
        'from_date'       => 'required_if:delete_type,date_range|nullable|date',
        'to_date'         => 'required_if:delete_type,date_range|nullable|date|after_or_equal:from_date',
        'status'          => 'required_if:delete_type,date_range|nullable|in:' . implode(',', $allowedStatuses),
    ]);

    DB::beginTransaction();

    try {

        $cutoffDate = now()->subDays(15);

        /*
        |--------------------------------------------------------------------------
        | DELETE BY APPOINTMENT NUMBER
        |--------------------------------------------------------------------------
        */
        if ($request->delete_type === 'appointment_no') {

            $appointment = Appointment::where(
                'appointment_no',
                trim($request->appointment_no)
            )->first();

            if (!$appointment) {
                return back()->withErrors([
                    'appointment_no' => 'Appointment not found.'
                ]);
            }

            if (!in_array($appointment->status, $allowedStatuses)) {
                return back()->withErrors([
                    'appointment_no' => 'This application cannot be deleted.'
                ]);
            }

            /*
            |----------------------------------------------------------
            | 🟢 ALWAYS ALLOW: Cancelled & Accepted
            |----------------------------------------------------------
            */
            if (!in_array($appointment->status, [
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_ACCEPTED,
            ])) {

                // 🔴 For other statuses enforce delivery + 15 days rule
                if (!in_array($appointment->delivery_status, [
                    Appointment::STATUS_COLLECTED,
                    Appointment::STATUS_DISPATCHED,
                ])) {
                    return back()->withErrors([
                        'appointment_no' => 'Only Collected or Dispatched applications can be deleted.'
                    ]);
                }

                if (!$appointment->delivery_status_changed_at ||
                    $appointment->delivery_status_changed_at > $cutoffDate) {

                    return back()->withErrors([
                        'appointment_no' => 'Applications can only be deleted 15 days after delivery.'
                    ]);
                }
            }

            $appointment->delete();

            DB::commit();

            return back()->with('success', 'Appointment deleted successfully.');
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE BY DATE RANGE
        |--------------------------------------------------------------------------
        */

        if (!in_array($request->status, $allowedStatuses)) {
            return back()->withErrors([
                'status' => 'This status cannot be deleted.This is already Under Process'
            ]);
        }

        $query = Appointment::whereBetween('appointment_date', [
                $request->from_date,
                $request->to_date
            ])
            ->where('status', $request->status);

        /*
        |----------------------------------------------------------
        | 🟢 ALWAYS ALLOW: Cancelled & Accepted
        |----------------------------------------------------------
        */
        if (!in_array($request->status, [
            Appointment::STATUS_CANCELLED,
            Appointment::STATUS_ACCEPTED,
        ])) {

            // 🔴 Enforce delivery + 15 days rule
            $query->whereIn('delivery_status', [
                Appointment::STATUS_COLLECTED,
                Appointment::STATUS_DISPATCHED,
            ])
            ->whereNotNull('delivery_status_changed_at')
            ->where('delivery_status_changed_at', '<=', $cutoffDate);
        }

        $deleted = $query->delete();

        DB::commit();

        if ($deleted === 0) {
            return back()->with('error', 'No eligible applications found for deletion.');
        }

        return back()->with(
            'success',
            $deleted . ' appointment(s) deleted successfully.'
        );

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()->with(
            'error',
            'Something went wrong: ' . $e->getMessage()
        );
    }
}

public function update(Request $request, Appointment $appointment)
{
    $validated = $request->validate([
        'service' => 'required|string',
        'ReferenceNr' => 'nullable|string|max:255',
        'passportno' => 'nullable|string|max:45',
    ]);

    $oldService = $appointment->service;

    $appointment->update([
        'service'     => $validated['service'],
        'ReferenceNr' => $validated['ReferenceNr'] ?? $appointment->ReferenceNr,
        'passportno'  => $validated['passportno'] ?? null,
    ]);

    // Log tracking if service changed
    
    
// Log tracking if service changed
if ($oldService !== $validated['service']) {

    $appointment->track(
        'service_changed',
        $appointment->current_assignee_id,
        "Service changed from {$oldService} to {$validated['service']}"
    );
}

return redirect()
    ->route('appointments.index')
    ->with('success', 'Appointment updated successfully.');


}

public function printLabel(Appointment $appointment)
    {
        // Prepare data
        $data = [
            'name'           => $appointment->applicant_name,
            'passport_no'    => $appointment->passportno ?? 'N/A',
            'application_no' => $appointment->appointment_no,
            'referenceno'    => $appointment->ReferenceNr,
        ];

        // Build ZPL
        $zpl = $this->buildZpl($data);

        // Ensure label directory exists
        $labelDir = storage_path('app/labels');
        if (!File::exists($labelDir)) {
            File::makeDirectory($labelDir, 0755, true);
        }

        // Create unique file
        $fileName = 'label_' . uniqid() . '.zpl';
        $filePath = $labelDir . DIRECTORY_SEPARATOR . $fileName;

        // Save file
        File::put($filePath, $zpl);

        if (!File::exists($filePath)) {
            Log::error("ZPL file not created: {$filePath}");
            return back()->with('error', 'Label could not be created.');
        }

        // Printer path (local to IIS server)
        $printerPath = '\\\\localhost\\VEVOR_Label_Printer';

        // Windows-safe command
        $command = 'cmd /c copy /B "' . $filePath . '" "' . $printerPath . '"';

        exec($command, $output, $resultCode);

        Log::info("Printing label...", [
            'printer'    => $printerPath,
            'file'       => $filePath,
            'command'    => $command,
            'output'     => $output,
            'resultCode' => $resultCode
        ]);

        if ($resultCode !== 0) {
            return back()->with('error', 'Label printing failed.');
        }

        // Delete file after successful print
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
$appointment->track(
    'label_printed',
    $appointment->current_assignee_id,
    'Application label printed successfully.'
);
        return back()->with('success', 'Label printed successfully.');
    }

private function buildZpl(array $data): string
{
    $name = strtoupper(trim($data['name']));
    $nameLength = strlen($name);

    // Optimized scaling for max 40 chars
    if ($nameLength <= 20) {
        $nameFont = 42;
    } elseif ($nameLength <= 30) {
        $nameFont = 34;
    } else {
        $nameFont = 28;   // for ~40 chars
    }

    $nameWidth = intval($nameFont * 0.7);

    $applicationNo = $data['application_no'];

    return "^XA
^PW600
^LL450

^FO10,30
^FB580,2,8,C,0
^A0N,{$nameFont},{$nameWidth}
^FD{$name}^FS

^FO10,130
^FB580,1,0,L,0
^A0N,28,20
^FDPassport: {$data['passport_no']}^FS

^FO10,170
^FB580,1,0,L,0
^A0N,28,20
^FDApp No: {$applicationNo}^FS

^FO45,220
^BY2
^BCN,90,Y,N,N
^FD{$applicationNo}^FS

^XZ";
}

public function history(Appointment $appointment)
{
    $appointment->load('trackings.assignedBy', 'trackings.assignedTo');

    return view('appointments.partials.history-content', compact('appointment'));
}





}
