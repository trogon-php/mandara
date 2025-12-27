<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Estore\AppEstoreCartResource;
use App\Services\Estore\EstoreCartService;
use Illuminate\Http\Request;

class EstoreCartController extends BaseApiController
{
    public function __construct(protected EstoreCartService $cartService) {}

    public function index(Request $request)
    {
        $user = $this->getAuthUser();

        $request->merge(['cartList' => true]);
        $items = $this->cartService->getCartItems($user->id);
        $items = AppEstoreCartResource::collection($items);

        $grandTotal = $items->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        
        $cart = [
            'items' => $items,
            'grand_total' => number_format($grandTotal, 2, '.', ''),
        ];
        return $this->respondSuccess($cart, 'Cart items retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:add,minus'
        ]);

        $user = $this->getAuthUser();
        $result = $this->cartService->addToCart($user->id, $data);

        return $this->serviceResponse($result);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $this->getAuthUser();
        $result = $this->cartService->updateCartItem($user->id, $id, $data);

        return $this->serviceResponse($result);
    }

    public function destroy($id)
    {
        $user = $this->getAuthUser();
        $result = $this->cartService->removeFromCart($user->id, $id);

        return $this->serviceResponse($result);
    }

    public function clear()
    {
        $user = $this->getAuthUser();
        $result = $this->cartService->clearCart($user->id);

        return $this->serviceResponse($result);
    }

    public function total()
    {
        $user = $this->getAuthUser();
        $result = $this->cartService->getCartTotal($user->id);

        return $this->serviceResponse($result);
    }

    public function checkout(Request $request)
    {
        $user = $this->getAuthUser();
        $result = $this->cartService->checkout($user->id);


        return $this->serviceResponse($result);
    }
}