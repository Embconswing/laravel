<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    /**
     * Supervisor inbox:
     * Shows applications returned specifically to the logged-in supervisor
     */
    public function inbox()
    {
        $appointments = Appointment::where('status', 'returned_to_supervisor')
            ->where('current_assignee_id', auth()->id()) // 🔥 CRITICAL FIX
            ->with([
                'trackings' => function ($query) {
                    $query->latest();
                },
                'trackings.assignedBy',
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('supervisor.inbox', compact('appointments'));
    }
}
