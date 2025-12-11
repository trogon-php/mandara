<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\Traits\AdminControllerHelpers;
use App\Http\Requests\Coupons\StoreCouponRequest;
use App\Http\Requests\Coupons\UpdateCouponRequest;
use App\Services\Coupons\CouponService;
use App\Services\Packages\PackageService;
use App\Services\Users\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends AdminBaseController
{
    use AdminControllerHelpers;
    
    protected CouponService $couponService;
    protected PackageService $packageService;
    protected UserService $userService;

    public function __construct(CouponService $couponService, PackageService $packageService, UserService $userService)
    {
        $this->couponService = $couponService;
        $this->packageService = $packageService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of coupons
     */
    public function index(Request $request): View
    {
        // Get all coupons with relationships for now (can be optimized later)
        $coupons = $this->couponService->getAllWithRelations();

        $filterConfig = $this->couponService->getFilterConfig();
        $searchConfig = $this->couponService->getSearchConfig();

        return view('admin.coupons.index', compact('coupons', 'filterConfig', 'searchConfig'));
    }

    /**
     * Show the form for creating a new coupon
     */
    public function create(): View
    {
        $packages = $this->packageService->getActivePackages();
        $users = $this->userService->getActiveUsers();
        
        return view('admin.coupons.create', compact('packages', 'users'));
    }

    /**
     * Store a newly created coupon
     */
    public function store(StoreCouponRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Double-check if coupon code already exists
            if ($this->couponService->codeExists($data['code'])) {
                return $this->errorResponse('This coupon code already exists.', ['code' => ['This coupon code already exists.']], 422);
            }
            
            // Handle package associations
            $packageIds = $data['package_ids'] ?? [];
            unset($data['package_ids']);
            
            // Handle user associations
            $userIds = $data['user_ids'] ?? [];
            unset($data['user_ids']);

            $coupon = $this->couponService->store($data);
            
            // Attach packages and users
            if (!empty($packageIds)) {
                $coupon->packages()->attach($packageIds);
            }
            
            if (!empty($userIds)) {
                $coupon->users()->attach($userIds);
            }

            return $this->successResponse('Coupon created successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                return $this->errorResponse('This coupon code already exists.', ['code' => ['This coupon code already exists.']], 422);
            }
            throw $e;
        }
    }

    /**
     * Display the specified coupon
     */
    public function show(int $id): View
    {
        $coupon = $this->couponService->find($id, ['packages', 'users', 'usages.user', 'usages.order']);
        
        if (!$coupon) {
            abort(404);
        }

        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(int $id): View
    {
        $coupon = $this->couponService->find($id, ['packages', 'users']);
        
        if (!$coupon) {
            abort(404);
        }

        $packages = $this->packageService->getActivePackages();
        $users = $this->userService->getActiveUsers();
        
        return view('admin.coupons.edit', compact('coupon', 'packages', 'users'));
    }

    /**
     * Update the specified coupon
     */
    public function update(UpdateCouponRequest $request, int $id)
    {
        $coupon = $this->couponService->find($id);
        
        if (!$coupon) {
            return $this->errorResponse('Coupon not found.', null, 404);
        }

        $data = $request->validated();
        
        // Handle package associations
        $packageIds = $data['package_ids'] ?? [];
        unset($data['package_ids']);
        
        // Handle user associations
        $userIds = $data['user_ids'] ?? [];
        unset($data['user_ids']);

        $this->couponService->update($id, $data);
        
        // Sync packages and users
        $coupon->packages()->sync($packageIds);
        $coupon->users()->sync($userIds);

        return $this->successResponse('Coupon updated successfully.');
    }

    /**
     * Remove the specified coupon
     */
    public function destroy(Request $request, int $id)
    {
        $coupon = $this->couponService->find($id);
        
        if (!$coupon) {
            return $this->errorResponse('Coupon not found.', null, 404);
        }

        // Check if coupon has been used
        if ($coupon->usages()->count() > 0) {
            return $this->errorResponse('Cannot delete coupon that has been used.', null, 422);
        }

        $this->couponService->delete($id);

        return $this->successResponse('Coupon deleted successfully.');
    }

    /**
     * Bulk delete coupons
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:coupons,id'
        ]);

        $ids = $request->get('ids');
        
        // Check if any coupon has been used
        $usedCoupons = $this->couponService->getUsedCoupons($ids)->count();
            
        if ($usedCoupons > 0) {
            return redirect()->route('admin.coupons.index')
                ->with('error', 'Cannot delete coupons that have been used.');
        }

        $deleted = $this->couponService->bulkDelete($ids);

        return redirect()->route('admin.coupons.index')
            ->with('success', "{$deleted} coupons deleted successfully.");
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $coupon = $this->couponService->find($id);
        
        if (!$coupon) {
            abort(404);
        }

        $newStatus = $coupon->status === 'active' ? 'inactive' : 'active';
        $this->couponService->update($id, ['status' => $newStatus]);

        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon status updated to {$newStatus}.");
    }

    /**
     * Get coupon usage statistics
     */
    public function usageStats(int $id): View
    {
        $coupon = $this->couponService->find($id, ['usages.user', 'usages.order']);
        
        if (!$coupon) {
            abort(404);
        }

        $stats = [
            'total_usage' => $coupon->usages()->count(),
            'unique_users' => $coupon->usages()->distinct('user_id')->count(),
            'recent_usage' => $coupon->usages()->where('used_at', '>=', now()->subDays(30))->count(),
            'usage_by_month' => $coupon->usages()
                ->selectRaw('DATE_FORMAT(used_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
        ];

        return view('admin.coupons.usage-stats', compact('coupon', 'stats'));
    }

    // Validate coupon code for AJAX
    public function validateCode($code)
    {
        $coupon = $this->couponService->findByCode($code);
        
        if (!$coupon || $coupon->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive coupon']);
        }

        // Check if coupon is expired
        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            return response()->json(['success' => false, 'message' => 'Coupon has expired']);
        }

        // Check if coupon is not yet active
        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            return response()->json(['success' => false, 'message' => 'Coupon is not yet active']);
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'title' => $coupon->title,
            ]
        ]);
    }
}
