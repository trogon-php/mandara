<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // If already authenticated in any panel, send to that dashboard
        foreach (['admin','tutor','student'] as $g) {
            if (Auth::guard($g)->check()) {
                return redirect()->to($this->dashboardPathFor($g));
            }
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:student,tutor'], // Only allow student and tutor registration
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role === 'tutor' ? 3 : 2, // 3 for tutor, 2 for student
        ]);

        // Auto-login after registration
        $guard = $request->role === 'tutor' ? 'tutor' : 'student';
        Auth::guard($guard)->login($user);

        return redirect()->intended($this->dashboardPathFor($guard));
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
