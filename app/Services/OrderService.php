<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @param array{
     *     items: array<int, array{
     *         product_id: int,
     *         quantity: int
     *     }>
     * } $data
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $total = 0;

            $order = Order::create([
                'total' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = $item['quantity'];

                if ($product->stock_quantity < $quantity) {
                    throw new InsufficientStockException(
                        productId: $product->id,
                        productName: $product->name,
                        available: $product->stock_quantity,
                        requested: $quantity,
                    );
                }

                $unitPrice = (float) $product->price;

                $subtotal = $unitPrice * $quantity;

                $product->decrement(
                    'stock_quantity',
                    $quantity
                );

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'total' => $total,
            ]);

            return $order->load('items.product');
        });
    }
}