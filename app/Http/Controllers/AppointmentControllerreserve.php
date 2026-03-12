<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display the appointments list.
     */
    public function index()
    {
        $appointments = Appointment::orderByRaw("
        CASE
            WHEN status = 'accepted' THEN 3
            WHEN window_no IS NOT NULL THEN 1
            ELSE 2
        END
    ")
    ->orderBy('start_time', 'asc')
    ->get();


        $usedWindows = Appointment::whereNotNull('window_no')
            ->where('status', '!=', 'accepted')
            ->pluck('window_no')
            ->toArray();

        return view('appointments.index', compact('appointments', 'usedWindows'));
    }
public function skip(Appointment $appointment)
{
    if ($appointment->status === 'accepted') {
        return back();
    }

    if (!$appointment->window_no) {
        return back();
    }

    $appointment->update([
        'status' => 'waiting',
        'window_no' => null,
    ]);

    return back();
}


    /**
     * Import appointments from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('csv_file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));

        // first row as header
        $header = array_map('trim', array_shift($rows));

        foreach ($rows as $row) {
            if (count($row) !== count($header)) {
                // skip malformed rows
                continue;
            }

            $data = array_combine($header, $row);

            Appointment::create([
                'appointment_number' => $data['appointment_number'],
                'ReferenceNr'        => $data['ReferenceNr'],
                'customer_name'      => $data['customer_name'],
                'Nationality'        => $data['Nationality'] ?? null,
                'window_no'          => $data['window_no'] ?: null,
                'status'             => $data['status'] ?? 'waiting',
                'appointment_date'   => $data['appointment_date'],
                'start_time'         => $data['start_time'],
            ]);
        }

        return redirect()
            ->route('appointments.index')
            ->with('success', 'CSV imported successfully.');
    }

    /**
     * Assign a window to an appointment.
     */
    public function assignWindow(Request $request, Appointment $appointment)
    {
        $request->validate([
            'window_no' => 'required|integer|between:1,4',
        ]);

        // Prevent assigning a window already in use by another appointment
        $windowInUse = Appointment::where('window_no', $request->window_no)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'accepted')
            ->exists();

        if ($windowInUse) {
            return back()->withErrors([
                'window_no' => 'This window is already in use.',
            ]);
        }

        $appointment->update([
            'window_no' => $request->window_no,
            'status'    => 'in_progress',
        ]);

        return back()->with('play_sound', true);
    }

    /**
     * Mark an appointment as completed and release its window.
     */
    public function complete(Appointment $appointment)
    {
        $appointment->update([
            'status'    => 'accepted',
            'window_no' => null, // release window
        ]);

        return back();
    }
}
