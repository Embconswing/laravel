<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show applications assigned to the logged-in user
     */
   public function myAssignments()
    {
        $user = auth()->user();

        $query = Appointment::query()
            ->where('status', '!=', Appointment::STATUS_PROCESS_COMPLETED);

        /*
        |--------------------------------------------------------------------------
        | NORMAL USER
        |--------------------------------------------------------------------------
        */
        if ($user->user_type === User::TYPE_NORMAL) {

            $query->where('current_assignee_id', $user->id);

        } else {

            /*
            |--------------------------------------------------------------------------
            | SUPERVISOR
            |--------------------------------------------------------------------------
            */
            $query->where(function ($q) use ($user) {
                $q->where('current_assignee_id', $user->id)
                  ->orWhere('status', Appointment::STATUS_RETURNED_TO_SUPERVISOR);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 🔥 FULL DATASET (FOR CHART)
        |--------------------------------------------------------------------------
        */

        $total = (clone $query)->count();

        /*
        |--------------------------------------------------------------------------
        | PENDING (assigned + pending)
        |--------------------------------------------------------------------------
        */
        $pending = (clone $query)
            ->whereIn('status', [
                Appointment::STATUS_ASSIGNED,
                Appointment::STATUS_PENDING,
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | UNDER PROCESS
        |--------------------------------------------------------------------------
        */
        $underProcess = (clone $query)
            ->where('status', Appointment::STATUS_PROCESS_IN_PROGRESS)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CANCELLED
        |--------------------------------------------------------------------------
        */
        $cancelled = (clone $query)
            ->where('status', Appointment::STATUS_CANCELLED)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PAGINATED TABLE (UNCHANGED)
        |--------------------------------------------------------------------------
        */
        $appointments = $query
            ->with([
                'trackings' => fn ($q) => $q->latest(),
                'trackings.assignedBy',
            ])
            ->orderBy('appointment_date')
            ->paginate(10)
            ->withQueryString();

        $users = User::where('user_type', User::TYPE_NORMAL)
            ->select('id', 'name', 'user_type')
            ->orderBy('name')
            ->get();

        return view('dashboard.my-assignments', compact(
            'appointments',
            'users',
            'total',
            'pending',
            'underProcess',
            'cancelled'
        ));
    }



}