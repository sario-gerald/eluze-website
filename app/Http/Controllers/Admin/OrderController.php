<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
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
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $activeStatus = $validated['status'] ?? null;
        $search = $validated['search'] ?? null;
        $orders = Order::query()
            ->with('items')
            ->status($activeStatus)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhere('delivery_address', 'like', "%{$search}%")
                        ->orWhere('tracking_number', 'like', "%{$search}%")
                        ->orWhereHas('items', fn ($query) => $query->where('product_name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $orderCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', [
            'activeStatus' => $activeStatus,
            'search' => $search,
            'orders' => $orders,
            'orderCounts' => $orderCounts,
            'totalOrders' => Order::query()->count(),
            'statuses' => Order::STATUSES,
        ]);
    }

    /**
     * Display one complete order record.
     */
    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order' => $order->load(['items.product', 'user']),
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

        $previousStatus = $order->status;
        $previousTrackingNumber = $order->tracking_number;

        $order->update([
            'status' => $validated['status'],
            'tracking_number' => $validated['tracking_number'] ?? $order->tracking_number,
        ]);

        $order->refresh();

        AuditLog::record(
            $request,
            'order.status_updated',
            "Updated {$order->order_reference} from {$previousStatus} to {$order->status}.",
            $order,
            [
                'order_reference' => $order->order_reference,
                'customer_name' => $order->customer_name,
                'previous_status' => $previousStatus,
                'new_status' => $order->status,
                'previous_tracking_number' => $previousTrackingNumber,
                'new_tracking_number' => $order->tracking_number,
            ]
        );

        return response()->json([
            'message' => 'Order status updated.',
            'order' => $order,
        ]);
    }
}
