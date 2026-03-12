<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;

class ApplicationPoolController extends Controller
{
    
public function index()
{
    $users = User::where('user_type', User::TYPE_NORMAL)->get();

    $appointments = Appointment::query()
        ->with(['latestAssignmentTracking', 'trackings'])
        ->whereHas('latestAssignmentTracking', function ($q) {
            $q->whereNotNull('assigned_to');
        })
        ->whereIn('status', [
            Appointment::STATUS_ASSIGNED,
            Appointment::STATUS_ACCEPTED,
            Appointment::STATUS_PENDING,
            Appointment::STATUS_PROCESS_IN_PROGRESS,
            Appointment::STATUS_PROCESS_COMPLETED,
            Appointment::STATUS_RETURNED_TO_SUPERVISOR,
        ])
       ->where(function ($q) {
    $q->whereNull('delivery_status')
      ->orWhere('delivery_status', '!=', Appointment::STATUS_COLLECTED);
})
        ->latest()
        ->get();

    $byAssignee = $appointments->groupBy(function ($appointment) {
        return optional($appointment->latestAssignmentTracking)->assigned_to;
    });

    $users->each(function ($user) use ($byAssignee) {
        $user->setRelation(
            'assignedAppointments',
            $byAssignee->get($user->id, collect())
        );
    });

    return view('admin.overview.application-pools', compact('users'));
}
}