<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

abstract class RoleBaseController extends Controller
{
    protected string $guard = 'web';
    protected ?int $requiredRoleId = null;

    public function __construct()
    {
        $this->middleware("auth:{$this->guard}");

        $this->middleware(function (\Illuminate\Http\Request $request, $next) {
            $user = $this->user();
            if (!$user) {
                return redirect()->route('login');
            }

            if ($this->requiredRoleId !== null && (int)$user->role_id !== (int)$this->requiredRoleId) {
                Auth::guard($this->guard)->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login');
            }

            View::share('authGuard', $this->guard);
            View::share('authUser', $user);

            return $next($request);
        });
    }

    protected function user(): ?Authenticatable
    {
        return Auth::guard($this->guard)->user();
    }

    protected function userId(): ?int
    {
        return $this->user()?->id;
    }

    protected function redirectToDashboard()
    {
        return redirect()->to('/');
    }
}
