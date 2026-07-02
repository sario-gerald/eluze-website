<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard overview.
     */
    public function index(): View
    {
        $orderCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard.index', [
            'recentOrders' => Order::query()->latest()->take(5)->get(),
            'totalOrders' => Order::query()->count(),
            'pendingOrders' => $orderCounts['pending'] ?? 0,
            'processingOrders' => $orderCounts['processing'] ?? 0,
            'deliveredOrders' => $orderCounts['delivered'] ?? 0,
            'todayOrders' => Order::query()->whereDate('created_at', today())->count(),
        ]);
    }
}
