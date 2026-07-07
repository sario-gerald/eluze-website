<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eluze Admin | {{ $order->order_reference }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}">
</head>
<body>
    <main class="admin-main admin-main--single">
        <header class="admin-header">
            <div>
                <p class="eyebrow">Order Details</p>
                <h1>{{ $order->order_reference }}</h1>
            </div>
            <a class="button button--ghost" href="{{ route('admin.orders.index') }}">Back to Orders</a>
        </header>
        <section class="metric-grid">
            <article class="metric-card"><p>Status</p><strong>{{ ucfirst($order->status) }}</strong></article>
            <article class="metric-card"><p>Total</p><strong>₱{{ number_format($order->total ?: $order->items->sum('line_total'), 2) }}</strong></article>
            <article class="metric-card"><p>Items</p><strong>{{ $order->items->sum('quantity') ?: 0 }}</strong></article>
        </section>
        <section class="orders-panel detail-grid">
            <div class="detail-card">
                <p class="eyebrow">Customer</p>
                <h2>{{ $order->customer_name }}</h2>
                <dl class="audit-meta">
                    <div><dt>Contact</dt><dd>{{ $order->contact_number }}</dd></div>
                    <div><dt>Account</dt><dd>{{ $order->user?->email ?? 'Legacy order' }}</dd></div>
                    <div><dt>Date Placed</dt><dd>{{ $order->created_at->format('M d, Y h:i A') }}</dd></div>
                </dl>
            </div>
            <div class="detail-card">
                <p class="eyebrow">Delivery</p>
                <h2>{{ $order->tracking_number ?: 'Awaiting tracking' }}</h2>
                <p>{{ $order->delivery_address }}</p>
            </div>
        </section>
        <section class="orders-panel">
            <div class="panel-heading">
                <h2>Purchased Items</h2>
                <p>Subtotal ₱{{ number_format($order->subtotal ?: $order->items->sum('line_total'), 2) }}</p>
            </div>
            <div class="table-wrap">
                <table class="orders-table">
                    <thead><tr><th>Product</th><th>Collection</th><th>Size</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse ($order->items as $item)
                            <tr>
                                <td><span class="order-id">{{ $item->product_name }}</span><span class="order-date">{{ $item->scent }}</span></td>
                                <td>{{ $item->collection }}</td>
                                <td>{{ $item->size_ml }}ml</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                <td>₱{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td class="empty-state" colspan="6">Items were not captured for this older order.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="order-total-stack">
                <span>Shipping: ₱{{ number_format($order->shipping_fee, 2) }}</span>
                <strong>Total: ₱{{ number_format($order->total ?: ($order->items->sum('line_total') + $order->shipping_fee), 2) }}</strong>
            </div>
        </section>
    </main>
</body>
</html>
