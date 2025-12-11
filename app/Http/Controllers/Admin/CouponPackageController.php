<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Coupons\CouponService;
use App\Services\Packages\PackageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponPackageController extends AdminBaseController
{
    protected CouponService $couponService;
    protected PackageService $packageService;

    public function __construct(CouponService $couponService, PackageService $packageService)
    {
        parent::__construct();
        $this->couponService = $couponService;
        $this->packageService = $packageService;
    }

    /**
     * Show packages for a specific coupon
     */
    public function index(int $couponId): View
    {
        $coupon = $this->couponService->findWithRelations($couponId, ['packages', 'packages.features']);
        if (!$coupon) {
            abort(404);
        }
        
        $availablePackages = $this->packageService->getActivePackages()
            ->whereNotIn('id', $coupon->packages->pluck('id'));

        return view('admin.coupons.packages.index', compact('coupon', 'availablePackages'));
    }

    /**
     * Attach packages to coupon
     */
    public function store(Request $request, int $couponId): RedirectResponse
    {
        $request->validate([
            'package_ids' => 'required|array|min:1',
            'package_ids.*' => 'integer|exists:packages,id',
        ]);

        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $packageIds = $request->get('package_ids');

        // Check if packages are already attached
        $existingPackageIds = $coupon->packages()->pluck('packages.id')->toArray();
        $newPackageIds = array_diff($packageIds, $existingPackageIds);

        if (empty($newPackageIds)) {
            return redirect()->route('admin.coupons.packages.index', $couponId)
                ->with('warning', 'Selected packages are already attached to this coupon.');
        }

        $coupon->packages()->attach($newPackageIds);

        return redirect()->route('admin.coupons.packages.index', $couponId)
            ->with('success', count($newPackageIds) . ' package(s) attached successfully.');
    }

    /**
     * Detach package from coupon
     */
    public function destroy(int $couponId, int $packageId): RedirectResponse
    {
        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        
        // Check if package is attached
        if (!$coupon->packages()->where('packages.id', $packageId)->exists()) {
            return redirect()->route('admin.coupons.packages.index', $couponId)
                ->with('error', 'Package is not attached to this coupon.');
        }

        $coupon->packages()->detach($packageId);

        return redirect()->route('admin.coupons.packages.index', $couponId)
            ->with('success', 'Package detached successfully.');
    }

    /**
     * Bulk detach packages
     */
    public function bulkDetach(Request $request, int $couponId): RedirectResponse
    {
        $request->validate([
            'package_ids' => 'required|array|min:1',
            'package_ids.*' => 'integer|exists:packages,id',
        ]);

        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $packageIds = $request->get('package_ids');

        $coupon->packages()->detach($packageIds);

        return redirect()->route('admin.coupons.packages.index', $couponId)
            ->with('success', count($packageIds) . ' package(s) detached successfully.');
    }

    /**
     * Sync all packages for coupon
     */
    public function sync(Request $request, int $couponId): RedirectResponse
    {
        $request->validate([
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'integer|exists:packages,id',
        ]);

        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $packageIds = $request->get('package_ids', []);

        $coupon->packages()->sync($packageIds);

        return redirect()->route('admin.coupons.packages.index', $couponId)
            ->with('success', 'Package associations updated successfully.');
    }

    /**
     * Get available packages for AJAX
     */
    public function getAvailablePackages(Request $request, int $couponId)
    {
        $coupon = $this->couponService->find($couponId);
        if (!$coupon) {
            abort(404);
        }
        $search = $request->get('search', '');
        
        $packages = $this->packageService->getActivePackages()
            ->whereNotIn('id', $coupon->packages->pluck('id'))
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'title', 'description', 'price']);

        return response()->json($packages);
    }
}
