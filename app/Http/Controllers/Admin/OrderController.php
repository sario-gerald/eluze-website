<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display the order management workspace.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(Order::STATUSES)],
        ]);

        $activeStatus = $validated['status'] ?? null;
        $orders = Order::query()
            ->status($activeStatus)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $orderCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', [
            'activeStatus' => $activeStatus,
            'orders' => $orders,
            'orderCounts' => $orderCounts,
            'totalOrders' => Order::query()->count(),
            'statuses' => Order::STATUSES,
        ]);
    }

    /**
     * Update an order's fulfillment status from the admin workspace.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
            'tracking_number' => [
                Rule::requiredIf(in_array($request->input('status'), ['processing', 'delivered'], true)),
                'nullable',
                'string',
                'max:120',
            ],
        ]);

        $order->update([
            'status' => $validated['status'],
            'tracking_number' => $validated['tracking_number'] ?? $order->tracking_number,
        ]);

        return response()->json([
            'message' => 'Order status updated.',
            'order' => $order->fresh(),
        ]);
    }
}
