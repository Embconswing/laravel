<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TvAppointmentController extends Controller
{
    public function configname()
    {
        $registname = DB::table('configuration')->value('Registname');
        return view('index', compact('registname'));
    }

    public function index(): JsonResponse
    {
        $today = Carbon::today()->toDateString();
        $now   = Carbon::now();

        $appointments = Appointment::whereDate('appointment_date', $today)
            ->where('status', '!=', 'completed')
            ->get()
            ->map(function ($a) {
                return [
                    'appointment_no'   => $a->appointment_no,
                    'name'             => $a->applicant_name,
                    'window_no'        => $a->window_no,
                    'status'           => $a->status,
                    'start_time'       => $a->appointment_start_time,
                    'end_time'         => $a->appointment_end_time,
                    'appointment_date' => $a->appointment_date,
                ];
            });

        $todayAppointments = Appointment::whereDate('appointment_date', $today)
            ->whereNotIn('status', ['completed','cancelled'])
            ->orderBy('appointment_start_time')
            ->pluck('appointment_no')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $appointments,
            'today_appointments' => $todayAppointments,
            'today_appointments_count' => count($todayAppointments),
            'fetched_at' => now()->toDateTimeString(),
        ]);
    }
}
