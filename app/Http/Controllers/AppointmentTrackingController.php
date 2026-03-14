<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentCollectionReadyMail;
use App\Models\DocumentCollection;

class AppointmentTrackingController extends Controller
{
    /* =========================================================
     | ASSIGNMENT SCREEN
     ========================================================= */
 public function index(Request $request)
{
    $query = Appointment::where('status', Appointment::STATUS_ACCEPTED)
        ->whereNull('current_assignee_id');


    /* 🔹 DATE FILTER */

    if ($request->filled('from_date')) {
        $query->whereDate('appointment_date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('appointment_date', '<=', $request->to_date);
    }

    // Default to today if no date selected
    if (!$request->filled('from_date') && !$request->filled('to_date')) {
        $query->whereDate('appointment_date', now());
    }


    /* 🔹 SERVICE FILTER */

    if ($request->filled('service')) {

        switch ($request->service) {

            case 'Passport':
                $query->where('service', 'like', 'Passport%');
                break;

            case 'Visa':
                $query->where('service', 'like', 'Visa%');
                break;

            case 'OCI':
                $query->where('service', 'like', 'OCI%');
                break;

            case 'Miscellaneous':
                $query->where(function ($q) {
                    $q->where('service', 'not like', 'Passport%')
                      ->where('service', 'not like', 'Visa%')
                      ->where('service', 'not like', 'OCI%');
                });
                break;
        }
    }


    /* 🔎 REFERENCE SEARCH */

    if ($request->filled('reference')) {
        $query->where('ReferenceNr', 'like', '%' . $request->reference . '%');
    }


    /* 🔹 ORDER */

    $query->orderBy('appointment_date', 'desc');

    $appointments = $query->get();


    /* 🔹 FALLBACK TO TODAY IF EMPTY */

    if ($appointments->isEmpty() && ($request->filled('from_date') || $request->filled('to_date'))) {

        session()->flash(
            'warning',
            'No applications available for the selected date..'
        );

        // rebuild base query for today
        $fallbackQuery = Appointment::where('status', Appointment::STATUS_ACCEPTED)
            ->whereNull('current_assignee_id')
            ->whereDate('appointment_date', now());

        // preserve service filter
        if ($request->filled('service')) {

            switch ($request->service) {

                case 'Passport':
                    $fallbackQuery->where('service', 'like', 'Passport%');
                    break;

                case 'Visa':
                    $fallbackQuery->where('service', 'like', 'Visa%');
                    break;

                case 'OCI':
                    $fallbackQuery->where('service', 'like', 'OCI%');
                    break;

                case 'Miscellaneous':
                    $fallbackQuery->where(function ($q) {
                        $q->where('service', 'not like', 'Passport%')
                          ->where('service', 'not like', 'Visa%')
                          ->where('service', 'not like', 'OCI%');
                    });
                    break;
            }
        }

        $appointments = $fallbackQuery
            ->orderBy('appointment_date', 'desc')
            ->get();
    }


    /* 🔹 USERS */

    $users = User::select('id', 'name')
        ->where('user_type', User::TYPE_NORMAL)
        ->orderBy('name')
        ->get();


    return view('appointments.stage-assign', compact('appointments', 'users'));
}




    /* =========================================================
     | BULK ASSIGN
     ========================================================= */
   public function bulkAssign(Request $request)
{
    $request->validate([
        'current_assignee_id' => 'required|exists:users,id',
        'appointment_ids'     => 'required|array|min:1',
        'appointment_ids.*'   => 'exists:appointments,id',
    ]);

    DB::transaction(function () use ($request) {

        foreach ($request->appointment_ids as $appointmentId) {

            // ✅ Get appointment instance
            $appointment = Appointment::findOrFail($appointmentId);

            // ✅ Store previous assignee before update
            $previousAssignee = $appointment->current_assignee_id;

            // ✅ Update appointment
            $appointment->update([
                'current_assignee_id' => $request->current_assignee_id,
                'status'              => Appointment::STATUS_ASSIGNED,
            ]);

            // ✅ Create tracking record
           \App\Models\AppointmentTracking::create([
    'appointment_id' => $appointment->id,
    'assigned_by'    => auth()->id(),
    'assigned_to'    => $request->current_assignee_id,
    'action'         => $previousAssignee
                        ? \App\Models\AppointmentTracking::ACTION_REASSIGNED
                        : \App\Models\AppointmentTracking::ACTION_ASSIGNED,
    'remarks'        => null,
]);


        

        }
    });

    return redirect()
        ->route('appointments.assign.index')
        ->with('success', 'Appointments assigned successfully.');
}



    /* =========================================================
     | APPLICATIONS BY STATUS
     ========================================================= */
    public function applicationsByStatus(Request $request)
{
    $query = Appointment::with([
        'trackings',
        'trackings.assignedBy',
        'trackings.assignedTo',
    ]);

    // 🔹 Application status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 🔹 Delivery status filter
    if ($request->filled('delivery_status')) {
        $query->where('delivery_status', $request->delivery_status);
    }

    // 🔹 Search filter
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('applicant_name', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%")
              ->orWhere('ReferenceNr', 'like', "%{$request->search}%")
              ->orWhere('appointment_no', 'like', "%{$request->search}%");
        });
    }

    $appointments = $query
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return view('dashboard.applications-by-status', compact('appointments'));
}

public function findByReference(Request $request)
{
    $request->validate([
        'reference' => 'required|string'
    ]);

    $reference = trim($request->reference);

    $appointment = Appointment::where('ReferenceNr', $reference)
        ->where('status', Appointment::STATUS_ACCEPTED)
        ->whereNull('current_assignee_id')
        ->first();

    if (!$appointment) {
        return response()->json([
            'success' => false,
            'message' => 'Application not found or already assigned.'
        ]);
    }

    return response()->json([
        'success' => true,
        'appointment' => [
            'id' => $appointment->id,
            'reference' => $appointment->ReferenceNr,
            'appointment_no' => $appointment->appointment_no,
            'applicant_name' => $appointment->applicant_name,
            'service' => $appointment->service,
            'appointment_date' => $appointment->appointment_date
        ]
    ]);
}

public function updateReferenceAjax(Request $request)
{
    $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
        'reference' => 'required|string|max:255'
    ]);

    // check duplicate
    $exists = Appointment::where('ReferenceNr', $request->reference)
        ->where('id', '!=', $request->appointment_id)
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'Reference number already exists.'
        ]);
    }

    $appointment = Appointment::findOrFail($request->appointment_id);

    $oldRef = $appointment->ReferenceNr;

    $appointment->ReferenceNr = $request->reference;
    $appointment->save();

    return response()->json([
        'success' => true,
        'reference' => $request->reference
    ]);
}

  

public function changeReference(Request $request, Appointment $appointment)
{
    if (!auth()->user()->isNormal()) {
        abort(403);
    }

    $request->validate([
        'reference_no' => 'required|string|max:255',
        'remark'       => 'required|string|max:1000',
    ]);

    $oldRef = $appointment->ReferenceNr;

    $appointment->update([
        'ReferenceNr' => $request->reference_no,
    ]);

    // ✅ Use constant + keep assigned_to context
    $appointment->trackings()->create([
        'assigned_to' => $appointment->current_assignee_id, // important
        'assigned_by' => auth()->id(),
        'action'      => AppointmentTracking::ACTION_REFERENCE_CHANGED,
        'remarks'     => "Reference changed from {$oldRef} to {$request->reference_no}. Reason: {$request->remark}",
    ]);

    return back()
        ->with('success', 'Reference number updated successfully.')
        ->with('reprint_id', $appointment->id);
}
public function changeEmail(Request $request, Appointment $appointment)
{
    if (!auth()->user()->isNormal()) {
        abort(403);
    }

    if ($appointment->status === Appointment::STATUS_PROCESS_COMPLETED) {
        return back()->withErrors('Email cannot be changed after process completion.');
    }

    $request->validate([
        'email'  => 'required|email|max:255',
        'remark' => 'required|string|max:1000',
    ]);

    $oldEmail = $appointment->email;

    $appointment->update([
        'email' => $request->email,
    ]);

    $appointment->trackings()->create([
        'assigned_to' => $appointment->current_assignee_id,
        'assigned_by' => auth()->id(),
        'action'      => AppointmentTracking::ACTION_EMAIL_CHANGED,
        'remarks'     => "Email changed from {$oldEmail} to {$request->email}. Reason: {$request->remark}",
    ]);

    return back()->with('success', 'Email updated successfully.');
}

}
