<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // If already authenticated in any panel, send to that dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $creds = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);


        $user = User::where('email', $creds['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        // Block non-admin-panel roles early
        if (!in_array($user->role_id, [
            Role::ADMIN,
            Role::DOCTOR,
            Role::NURSE,
            Role::ATTENDANT,
            Role::ESTORE_DELIVERY_STAFF,
        ], true)) {
            return back()->withErrors(['email' => 'Not authorized for admin panel.']);
        }

        if (!Auth::guard('admin')->attempt($creds, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        foreach (['admin','doctor','nurse','attendant','estore_delivery_staff','web'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function roleMatchesGuard(int $roleId, string $guard): bool
    {
        return match ($guard) {
            'admin'   => in_array($roleId, [1, 3, 4, 5, 6]),
            'doctor'   => $roleId === 3,
            'nurse'   => $roleId === 4,
            'attendant'   => $roleId === 5,
            'estore_delivery_staff'   => $roleId === 6,
            default   => true,
        };
    }

    private function dashboardPathFor(string $guard): string
    {
        return match ($guard) {
            'admin'   => route('admin.dashboard'),
            'nurse'   => route('nurse.dashboard'),
            'student' => route('student.dashboard'),
            default   => url('/'),
        };
    }
}