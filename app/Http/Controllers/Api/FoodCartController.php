<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Food\AppFoodCartResource;
use App\Services\Food\FoodCartService;
use Illuminate\Http\Request;

class FoodCartController extends BaseApiController
{
    public function __construct(protected FoodCartService $cartService) {}

    public function index(Request $request)
    {
        $user = $this->getAuthUser();

        $request->merge(['cartList' => true]);
        $items = $this->cartService->getCartItems($user->id, true);
        $items = AppFoodCartResource::collection($items);

        $grandTotal = $items->sum(function($item) {
            return $item->item->price * $item->quantity;
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
            'food_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'action' => 'nullable|in:add,minus'
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