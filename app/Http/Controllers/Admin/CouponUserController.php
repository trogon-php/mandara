<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Coupons\CouponService;
use App\Services\Users\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponUserController extends AdminBaseController
{
    protected CouponService $couponService;
    protected UserService $userService;

    public function __construct(CouponService $couponService, UserService $userService)
    {
        parent::__construct();
        $this->couponService = $couponService;
        $this->userService = $userService;
    }

    /**
     * Show users for a specific coupon
     */
    public function index(int $couponId): View
    {
        $coupon = $this->couponService->findWithRelations($couponId, ['users']);
        if (!$coupon) {
            abort(404);
        }
        
        $availableUsers = $this->userService->getActiveUsers()
            ->whereNotIn('id', $coupon->users->pluck('id'))
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.coupons.users.index', compact('coupon', 'availableUsers'));
    }

    /**
     * Attach users to coupon
     */
    public function store(Request $request, int $couponId): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $userIds = $request->get('user_ids');

        // Check if users are already attached
        $existingUserIds = $coupon->users()->pluck('users.id')->toArray();
        $newUserIds = array_diff($userIds, $existingUserIds);

        if (empty($newUserIds)) {
            return redirect()->route('admin.coupons.users.index', $couponId)
                ->with('warning', 'Selected users are already attached to this coupon.');
        }

        $coupon->users()->attach($newUserIds);

        return redirect()->route('admin.coupons.users.index', $couponId)
            ->with('success', count($newUserIds) . ' user(s) attached successfully.');
    }

    /**
     * Detach user from coupon
     */
    public function destroy(int $couponId, int $userId): RedirectResponse
    {
        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        
        // Check if user is attached
        if (!$coupon->users()->where('users.id', $userId)->exists()) {
            return redirect()->route('admin.coupons.users.index', $couponId)
                ->with('error', 'User is not attached to this coupon.');
        }

        $coupon->users()->detach($userId);

        return redirect()->route('admin.coupons.users.index', $couponId)
            ->with('success', 'User detached successfully.');
    }

    /**
     * Bulk detach users
     */
    public function bulkDetach(Request $request, int $couponId): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $userIds = $request->get('user_ids');

        $coupon->users()->detach($userIds);

        return redirect()->route('admin.coupons.users.index', $couponId)
            ->with('success', count($userIds) . ' user(s) detached successfully.');
    }

    /**
     * Sync all users for coupon
     */
    public function sync(Request $request, int $couponId): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $userIds = $request->get('user_ids', []);

        $coupon->users()->sync($userIds);

        return redirect()->route('admin.coupons.users.index', $couponId)
            ->with('success', 'User associations updated successfully.');
    }

    /**
     * Get available users for AJAX
     */
    public function getAvailableUsers(Request $request, int $couponId)
    {
        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $search = $request->get('search', '');
        
        $users = $this->userService->getActiveUsers()
            ->whereNotIn('id', $coupon->users->pluck('id'))
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($users);
    }

    /**
     * Show user usage statistics for coupon
     */
    public function usageStats(int $couponId, int $userId): View
    {
        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $user = $this->userService->find($userId);
        if (!$user) {
            abort(404);
        }
        
        $usageStats = $coupon->usages()
            ->where('user_id', $userId)
            ->with('order')
            ->orderBy('used_at', 'desc')
            ->get();

        return view('admin.coupons.users.usage-stats', compact('coupon', 'user', 'usageStats'));
    }
}
