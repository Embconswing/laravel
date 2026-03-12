<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentTracking; 

class WalkinController extends Controller
{
    /**
     * Display today's walk-in applications
     */
    public function index()
    {
        $walkins = Appointment::query()
            ->whereNull('appointment_start_time')
            ->whereNull('appointment_end_time')
            ->whereDate('appointment_date', today())
            ->orderByDesc('created_at')
            ->paginate(15);

        $nationalities = Nationality::orderBy('Nationname')->get();

        return view('walk-ins.index', compact('walkins', 'nationalities'));
    }

    /**
     * Show create walk-in form
     */
    public function create()
    {
        $nationalities = Nationality::orderBy('Nationname')->get();

        return view('walk-ins.create', compact('nationalities'));
    }

    /**
     * Store a new walk-in application
     */
public function store(Request $request)
{
    $NEW_BORN_SERVICE = 'Passport-Birth Registration & Fresh passport to New Born';

    $rules = [
        'applicant_name' => 'required|string|max:255',
        'gender' => 'nullable|string',
        'nationality' => 'required',
        'mobile_number' => 'required|string|max:20',
        'email' => 'nullable|email',
        'date_of_birth' => 'nullable|date',
        'service' => 'required|string',
        'ReferenceNr' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'passportno' => 'nullable|string|max:45',
    ];

    if (trim((string) $request->service) === $NEW_BORN_SERVICE) {
        $rules['passport_holder_name'] = 'required|string|max:255';
    }

    $validated = $request->validate($rules);

    $nation = Nationality::findOrFail($validated['nationality']);
    $nationalityName = strtoupper($nation->Nationname);

    // Reference number logic
    if (str_starts_with($validated['service'], 'Misc -')) {
        $referenceNr = $this->generateReferenceNo();
    } else {

        if (empty($validated['ReferenceNr'])) {
            return back()
                ->withErrors(['ReferenceNr' => 'Reference Number is required for this service.'])
                ->withInput();
        }

        $referenceNr = $validated['ReferenceNr'];
    }

    DB::transaction(function () use ($validated, $referenceNr, $nationalityName, $NEW_BORN_SERVICE) {

        $common = [
            'appointment_date'       => today(),
            'appointment_start_time' => null,
            'appointment_end_time'   => null,
            'processed_by'           => null,
            'current_assignee_id'    => null,
            'gender'                 => $validated['gender'] ?? null,
            'nationality'            => $nationalityName,
            'mobile_number'          => $validated['mobile_number'],
            'email'                  => $validated['email'] ?? null,
            'date_of_birth'          => $validated['date_of_birth'] ?? null,
            'address'                => $validated['address'] ?? null,
            'status'                 => 'waiting',
            'passportno'             => $validated['passportno'] ?? null,
        ];

        /**
         * =========================
         * CASE 1: TWO APPLICATIONS
         * =========================
         */
        if ($validated['service'] === $NEW_BORN_SERVICE) {

            $reference1 = $referenceNr;
            $reference2 = $referenceNr . '1';

            $a1 = Appointment::create($common + [
                'appointment_no' => $this->generateAppointmentNo(),
                'ReferenceNr'    => $reference1,
                'service'        => 'Misc-Birth Registration',
                'applicant_name' => $validated['applicant_name'],
            ]);

            $a1->track(
                'walkin_created',
                null,
                'Walk-in created by ' . auth()->user()->name
            );

            $a2 = Appointment::create($common + [
                'appointment_no' => $this->generateAppointmentNo(),
                'ReferenceNr'    => $reference2,
                'service'        => 'Passport - Fresh passport to New Born',
                'applicant_name' => $validated['passport_holder_name'],
            ]);

            $a2->track(
                'walkin_created',
                null,
                'Walk-in created by ' . auth()->user()->name
            );

            return;
        }

        /**
         * =========================
         * CASE 2: SINGLE APPLICATION
         * =========================
         */
        $a = Appointment::create($common + [
            'appointment_no' => $this->generateAppointmentNo(),
            'ReferenceNr'    => $referenceNr,
            'service'        => $validated['service'],
            'applicant_name' => $validated['applicant_name'],
        ]);

        $a->track(
            'walkin_created',
            null,
            'Walk-in created by ' . auth()->user()->name
        );
    });

    return redirect()
        ->route('walkins.index')
        ->with('success', 'Walk-in application(s) created successfully.');
}

private function generateAppointmentNo(): string
    {


do {
    $number = 'BN-WN-' . now()->format('Ymd') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
} while (Appointment::where('appointment_no', $number)->exists());
        return $number;
    }

    /**
     * Generate Reference Number for Misc Services
     * Format: MBERYYYYMMDD001
     */
    private function generateReferenceNo(): string
    {
        $today = Carbon::now()->format('Ymd');

        return DB::transaction(function () use ($today) {

            // Lock rows for today to prevent duplicate numbers
            $lastRecord = Appointment::where('ReferenceNr', 'like', "MBER{$today}%")
                ->lockForUpdate()
                ->orderBy('ReferenceNr', 'desc')
                ->first();

            if ($lastRecord) {
                $lastNumber = (int) substr($lastRecord->ReferenceNr, -3);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            return "MBER{$today}" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}
