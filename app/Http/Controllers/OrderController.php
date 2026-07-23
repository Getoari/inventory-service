<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Display a listing of orders.
     */
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->with('items.product')
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request): OrderResource
    {
        $order = $this->orderService->createOrder(
            $request->validated()
        );

        return new OrderResource($order);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): OrderResource
    {
        $order->load('items.product');

        return new OrderResource($order);
    }
}