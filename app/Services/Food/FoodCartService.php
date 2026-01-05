<?php

namespace App\Services\Food;

use App\Models\FoodCart;
use App\Models\FoodItem;
use App\Services\Core\BaseService;
use Illuminate\Http\Response;

class FoodCartService extends BaseService
{
    protected string $modelClass = FoodCart::class;

    public function getFilterConfig(): array
    {
        return [];
    }

    public function getSearchFieldsConfig(): array
    {
        return [];
    }

    public function getDefaultSearchFields(): array
    {
        return [];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    public function getCartItems(int $userId, bool $withItem = false)
    {
        $query = $this->model->where('user_id', $userId);
        
        if ($withItem) {
            $query->with('item.category');
        }
        
        return $query->get();
    }

    public function addToCart(int $userId, array $data): array
    {
        $item = FoodItem::active()->inStock()->find($data['food_item_id']);
        
        if (!$item) {
            return [
                'status' => false,
                'message' => 'Food item not found or out of stock',
                'http_code' => Response::HTTP_OK
            ];
        }

        // Check if category is available now
        if (!$item->category || !$item->category->status) {
            return [
                'status' => false,
                'message' => 'This item is not available',
                'http_code' => Response::HTTP_OK
            ];
        }

        $currentTime = now()->format('H:i');
        // dd($currentTime,$item->category->start_time->format('H:i'),$item->category->end_time->format('H:i'));
        if ($item->category->start_time->format('H:i') > $currentTime || $item->category->end_time->format('H:i') < $currentTime) {
            return [
                'status' => false,
                'message' => 'This item is not available for ordering at this time',
                'http_code' => Response::HTTP_OK
            ];
        }

        // Check stock
        $quantity = $data['quantity'] ?? 1;
        if ($item->stock < $quantity) {
            return [
                'status' => false,
                'message' => 'Insufficient stock',
                'http_code' => Response::HTTP_OK
            ];
        }

        $cartItem = $this->model->where('user_id', $userId)
            ->where('food_item_id', $data['food_item_id'])
            ->first();

        if ($cartItem) {
            if (isset($data['action']) && $data['action'] == 'minus') {
                $newQuantity = $cartItem->quantity - $quantity;
                if ($newQuantity < 1) {
                    return [
                        'status' => false,
                        'message' => 'Quantity cannot be less than 1',
                        'http_code' => Response::HTTP_OK
                    ];
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
                $message = 'Quantity updated';
            } else {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($item->stock < $newQuantity) {
                    return [
                        'status' => false,
                        'message' => 'Insufficient stock',
                        'http_code' => Response::HTTP_OK
                    ];
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
                $message = 'Quantity updated';
            }
        } else {
            $cartItem = $this->model->create([
                'user_id' => $userId,
                'food_item_id' => $data['food_item_id'],
                'quantity' => $quantity,
            ]);
            $message = 'Item added to cart';
        }

        return [
            'status' => true,
            'message' => $message,
            'http_code' => Response::HTTP_OK
        ];
    }

    public function updateCartItem(int $userId, int $cartId, array $data): array
    {
        $cartItem = $this->model->where('user_id', $userId)->find($cartId);
        
        if (!$cartItem) {
            return [
                'status' => false,
                'message' => 'Cart item not found',
                'http_code' => Response::HTTP_NOT_FOUND
            ];
        }

        if (isset($data['quantity']) && $data['quantity'] < 1) {
            return [
                'status' => false,
                'message' => 'Quantity cannot be less than 1',
                'http_code' => Response::HTTP_BAD_REQUEST
            ];
        }

        // Check stock
        if (isset($data['quantity'])) {
            $item = $cartItem->item;
            if ($item->stock < $data['quantity']) {
                return [
                    'status' => false,
                    'message' => 'Insufficient stock',
                    'http_code' => Response::HTTP_BAD_REQUEST
                ];
            }
        }

        $cartItem->update($data);

        return [
            'status' => true,
            'message' => 'Cart updated',
            'http_code' => Response::HTTP_OK
        ];
    }

    public function removeFromCart(int $userId, int $cartId): array
    {
        $cartItem = $this->model->where('user_id', $userId)->find($cartId);
        
        if (!$cartItem) {
            return [
                'status' => false,
                'message' => 'Cart item not found',
                'http_code' => Response::HTTP_NOT_FOUND
            ];
        }

        $cartItem->delete();

        return [
            'status' => true,
            'message' => 'Item removed from cart',
            'http_code' => Response::HTTP_OK
        ];
    }

    public function clearCart(int $userId): array
    {
        $this->model->where('user_id', $userId)->delete();

        return [
            'status' => true,
            'message' => 'Cart cleared',
            'http_code' => Response::HTTP_OK
        ];
    }

    public function getCartTotal(int $userId): array
    {
        $items = $this->getCartItems($userId, true);
        $total = 0;

        foreach ($items as $item) {
            if ($item->item) {
                $total += $item->item->price * $item->quantity;
            }
        }

        return [
            'status' => true,
            'data' => [
                'total' => number_format($total, 2, '.', ''),
                'item_count' => $items->count()
            ],
            'http_code' => Response::HTTP_OK
        ];
    }

    public function checkout(string $userId): array
    {
        $result = $this->getCartTotal($userId);
        $discount = 0;
        return [
            'status' => true,
            'data' => [
                'sub_total' => number_format($result['data']['total'], 2, '.', ''),
                'discount' => number_format($discount, 2, '.', ''),
                'grand_total' => number_format($result['data']['total'] - $discount, 2, '.', ''),
                'razorpay_key' => config('services.razorpay.key_id'),
            ],
            'http_code' => Response::HTTP_OK
        ];
    }
}