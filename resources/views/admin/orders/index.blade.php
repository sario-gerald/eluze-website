<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Eluze Admin | Orders</title>
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['public/css/admin/orders.css', 'public/js/admin.js', 'public/js/admin/orders.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}">
        <script src="{{ asset('js/admin.js') }}" defer></script>
        <script src="{{ asset('js/admin/orders.js') }}" defer></script>
    @endif
</head>
<body>
    <div class="admin-shell">
        <div class="sidebar-overlay" data-sidebar-overlay></div>

        <aside class="admin-sidebar" data-admin-sidebar aria-label="Admin navigation">
            <a class="brand-mark" href="{{ route('admin.dashboard') }}" aria-label="Eluze admin dashboard">
                <img src="{{ asset('images/eluze_logo.png') }}" alt="Eluze">
            </a>
            <nav class="admin-nav">
                <a class="admin-nav__link" href="{{ route('admin.dashboard') }}" aria-label="Dashboard">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M3 11.5 12 4l9 7.5"></path>
                        <path d="M5 10.5V20h14v-9.5"></path>
                        <path d="M9 20v-6h6v6"></path>
                    </svg>
                    <span class="admin-nav__text">Dashboard</span>
                </a>
                <a class="admin-nav__link admin-nav__link--active" href="{{ route('admin.orders.index') }}" aria-label="Orders">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M7 7h14l-2 9H8L6 4H3"></path>
                        <path d="M9 20h.01"></path>
                        <path d="M18 20h.01"></path>
                    </svg>
                    <span class="admin-nav__text">Orders</span>
                </a>
            </nav>
            <form class="logout-form" method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="logout-button" type="submit" aria-label="Logout">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M21 3v18h-7"></path>
                    </svg>
                    <span class="admin-nav__text">Logout</span>
                </button>
            </form>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div class="admin-title-block">
                    <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle admin sidebar" aria-expanded="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <p class="eyebrow">Admin Dashboard</p>
                    <h1>Order Management</h1>
                </div>
                <div class="admin-header__meta">
                    <span>{{ now()->format('M d, Y') }}</span>
                </div>
            </header>

            <section class="status-tabs" aria-label="Filter orders by status">
                <a class="status-tab {{ $activeStatus === null ? 'status-tab--active' : '' }}" href="{{ route('admin.orders.index') }}">
                    All
                    <span>{{ $totalOrders }}</span>
                </a>
                @foreach ($statuses as $status)
                    <a class="status-tab {{ $activeStatus === $status ? 'status-tab--active' : '' }}" href="{{ route('admin.orders.index', ['status' => $status]) }}">
                        {{ ucfirst($status) }}
                        <span>{{ $orderCounts[$status] ?? 0 }}</span>
                    </a>
                @endforeach
            </section>

            <section class="orders-panel" aria-labelledby="orders-heading">
                <div class="panel-heading">
                    <h2 id="orders-heading">Orders</h2>
                    <p>{{ $orders->total() }} {{ \Illuminate\Support\Str::plural('record', $orders->total()) }}</p>
                </div>

                <div class="table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th scope="col">Order</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Delivery Address</th>
                                <th scope="col">Tracking</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr data-order-row="{{ $order->id }}">
                                    <td>
                                        <span class="order-id">#{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="order-date">{{ $order->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->contact_number }}</td>
                                    <td>{{ $order->delivery_address }}</td>
                                    <td data-tracking-cell>{{ $order->tracking_number ?? 'Awaiting tracking' }}</td>
                                    <td>
                                        <span class="status-pill status-pill--{{ $order->status }}" data-status-pill>{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="action-group" data-update-url="{{ route('admin.orders.update-status', $order) }}">
                                            @foreach ($statuses as $status)
                                                <button
                                                    class="status-action"
                                                    type="button"
                                                    data-status-action
                                                    data-status="{{ $status }}"
                                                    data-current-status="{{ $order->status }}"
                                                    data-current-tracking="{{ $order->tracking_number }}"
                                                    @disabled($order->status === $status)
                                                >
                                                    {{ ucfirst($status) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="empty-state" colspan="7">No orders found for this view.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrap">
                    {{ $orders->links() }}
                </div>
            </section>
        </main>
    </div>

    <div class="modal-backdrop" data-tracking-modal hidden>
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="tracking-modal-title">
            <button class="modal__close" type="button" data-modal-close aria-label="Close tracking modal">&times;</button>
            <p class="eyebrow">Fulfillment</p>
            <h2 id="tracking-modal-title">Add Tracking Number</h2>
            <form data-tracking-form>
                <input type="hidden" name="status" data-modal-status>
                <input type="hidden" name="url" data-modal-url>
                <label for="tracking-number">Tracking number</label>
                <input id="tracking-number" name="tracking_number" type="text" data-modal-tracking autocomplete="off" required>
                <p class="form-error" data-modal-error hidden></p>
                <div class="modal__actions">
                    <button class="button button--ghost" type="button" data-modal-cancel>Cancel</button>
                    <button class="button button--primary" type="submit">Update Order</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
