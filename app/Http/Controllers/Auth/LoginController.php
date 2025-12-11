<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // If already authenticated in any panel, send to that dashboard
        foreach (['admin','tutor','student'] as $g) {
            if (Auth::guard($g)->check()) {
                return redirect()->to($this->dashboardPathFor($g));
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $creds = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $creds['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        // Resolve guard by role_id
        $guard = match ((int) $user->role_id) {
            1 => 'admin',   // Admin
            3 => 'tutor',   // Tutor
            2 => 'student', // Student
            default => 'web',
        };

        if (Auth::guard($guard)->attempt($creds, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Defense-in-depth: ensure user’s role really matches guard
            if (!$this->roleMatchesGuard((int)$user->role_id, $guard)) {
                Auth::guard($guard)->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Not authorized for this panel.']);
            }

            return redirect()->intended($this->dashboardPathFor($guard));
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        foreach (['admin','tutor','student','web'] as $guard) {
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
            'admin'   => $roleId === 1,
            'tutor'   => $roleId === 3,
            'student' => $roleId === 2,
            default   => true,
        };
    }

    private function dashboardPathFor(string $guard): string
    {
        return match ($guard) {
            'admin'   => route('admin.dashboard'),
            'tutor'   => route('tutor.dashboard'),
            'student' => route('student.dashboard'),
            default   => url('/'),
        };
    }
}