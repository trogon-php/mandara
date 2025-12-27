<?php

namespace App\Services\Estore;

use App\Models\EstoreCart;
use App\Models\EstoreProduct;
use App\Services\Core\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EstoreCartService extends BaseService
{
    protected string $modelClass = EstoreCart::class;

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

    public function getCartItems(string $userId, bool $withProduct = false)
    {
        $query = $this->model->where('user_id', $userId);
        if ($withProduct) {
            $query->with('product.category');
        }
        return $query->get();
    }

    public function addToCart(string $userId, array $data): array
    {
        $product = EstoreProduct::active()->find($data['product_id']);
        
        if (!$product) {
            return [
                'status' => false,
                'message' => 'Product not found',
                'http_code' => Response::HTTP_OK
            ];
        }

        if ($product->stock < $data['quantity']) {
            return [
                'status' => false,
                'message' => 'Insufficient stock',
                'http_code' => Response::HTTP_OK
            ];
        }

        $cartItem = $this->model->where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($cartItem) {
            if ($data['action'] == 'minus') {
                $newQuantity = $cartItem->quantity - $data['quantity'];
                if ($newQuantity < 1) {
                    return [
                        'status' => false,
                        'message' => 'Quantity cannot be less than 1',
                        'http_code' => Response::HTTP_OK
                    ];
                }
                $message = '';
            } else {

                $newQuantity = $cartItem->quantity + $data['quantity'];
                if ($product->stock < $newQuantity) {
                    return [
                        'status' => false,
                        'message' => 'Insufficient stock',
                        'http_code' => Response::HTTP_OK
                    ];
                }
                $message = '';
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            $cartItem = $this->model->create([
                'user_id' => $userId,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
            ]);
            $message = 'Item added to cart';
        }

        return [
            'status' => true,
            'message' => $message,
            // 'data' => $cartItem->load('product.category'),
            'http_code' => Response::HTTP_OK
        ];
    }

    public function updateCartItem(string $userId, int $cartId, array $data): array
    {
        $cartItem = $this->model->where('user_id', $userId)->find($cartId);
        
        if (!$cartItem) {
            return [
                'status' => false,
                'message' => 'Cart item not found',
                'http_code' => Response::HTTP_NOT_FOUND
            ];
        }

        $product = EstoreProduct::find($cartItem->product_id);
        
        if ($product->stock < $data['quantity']) {
            return [
                'status' => false,
                'message' => 'Insufficient stock',
                'http_code' => Response::HTTP_BAD_REQUEST
            ];
        }

        $cartItem->quantity = $data['quantity'];
        $cartItem->save();

        return [
            'status' => true,
            'message' => 'Cart updated',
            // 'data' => $cartItem->load('product.category'),
            'http_code' => Response::HTTP_OK
        ];
    }

    public function removeFromCart(string $userId, int $cartId): array
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

    public function clearCart(string $userId): array
    {
        $this->model->where('user_id', $userId)->delete();

        return [
            'status' => true,
            'message' => 'Cart cleared',
            'http_code' => Response::HTTP_OK
        ];
    }

    public function getCartTotal(string $userId): array
    {
        $items = $this->getCartItems($userId);
        $total = 0;

        foreach ($items as $item) {
            $total += $item->product->price * $item->quantity;
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
                'delivery_note' => $this->getDeliveryText(),
            ],
            'http_code' => Response::HTTP_OK
        ];
    }

    function getDeliveryText(): string
    {
        $now = Carbon::now();
        $time = $now->format('H:i');

        // 06:00 AM → 08:59 PM
        if ($time >= '06:00' && $time <= '20:59') {
            return 'Your order will be delivered within 1 hour';
        }

        // 09:00 PM → 05:59 AM (overnight range)
        return 'Your order will be delivered by morning';
    }
}