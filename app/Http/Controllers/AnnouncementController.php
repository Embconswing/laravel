<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Exports\AppointmentsExport;
use Maatwebsite\Excel\Facades\Excel;
class AnnouncementController extends Controller
{
   
 

public function api()
{
    $announcements = Announcement::where(function ($q) {
            $q->where('is_active', 1)
              ->orWhereNull('is_active');
        })
        ->where(function ($q) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', now());
        })
        ->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        })
        ->orderBy('created_at', 'desc')
        ->get();

    $messages = [];

    foreach ($announcements as $a) {
        if (!$a->message) {
            continue;
        }

        // ✅ Decode JSON string into array
        $decoded = json_decode($a->message, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $messages = array_merge($messages, $decoded);
        } else {
            // fallback if message is plain text
            $messages[] = $a->message;
        }
    }

    return response()->json([
        'messages' => $messages
    ]);
}





public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Announcement::create($data);

        return redirect()
            ->route('announcements.index')
            ->with('success', 'Announcement added successfully');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $announcement->update($data);

        return back()->with('success', 'Announcement updated successfully');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully');
    }
}
