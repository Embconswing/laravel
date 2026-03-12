<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        return match ($user->user_type) {

            // 🧑‍💼 Supervisor → My Assignments
            User::TYPE_SUPERVISOR =>
                redirect()->route('my.assignments'),

            // 🧑‍💻 Officer → Stage Assign
            User::TYPE_OFFICER =>
                redirect()->route('appointments.assign.index'),

            // 👑 Admin → Announcements
            User::TYPE_ADMIN =>
                redirect()->route('announcements.index'),

            // 🔁 Fallback
            default =>
                redirect()->route('dashboard'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
