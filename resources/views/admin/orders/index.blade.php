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
                <a class="admin-nav__link" href="{{ route('admin.products.index') }}" aria-label="Products">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M6 3h12l1 6H5l1-6Z"></path>
                        <path d="M5 9v12h14V9"></path>
                        <path d="M9 13h6"></path>
                    </svg>
                    <span class="admin-nav__text">Products</span>
                </a>
                <a class="admin-nav__link" href="{{ route('admin.audit-trail.index') }}" aria-label="Audit Trail">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M12 8v5l3 2"></path>
                        <path d="M21 12a9 9 0 1 1-3-6.7"></path>
                        <path d="M21 4v5h-5"></path>
                    </svg>
                    <span class="admin-nav__text">Audit Trail</span>
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
                    <a class="status-tab {{ $activeStatus === $status ? 'status-tab--active' : '' }}" href="{{ route('admin.orders.index', array_filter(['status' => $status, 'search' => $search])) }}">
                        {{ ucfirst($status) }}
                        <span>{{ $orderCounts[$status] ?? 0 }}</span>
                    </a>
                @endforeach
            </section>

            <section class="orders-panel" aria-labelledby="orders-heading">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">Search and Fulfillment</p>
                        <h2 id="orders-heading">Orders</h2>
                    </div>
                    <p>{{ $orders->total() }} {{ \Illuminate\Support\Str::plural('record', $orders->total()) }}</p>
                </div>

                <form class="admin-filter-form" method="GET" action="{{ route('admin.orders.index') }}">
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search customer, contact, address, product, tracking">
                    @if ($activeStatus)
                        <input type="hidden" name="status" value="{{ $activeStatus }}">
                    @endif
                    <button class="button button--primary" type="submit">Search</button>
                    @if ($search || $activeStatus)
                        <a class="button button--ghost" href="{{ route('admin.orders.index') }}">Clear</a>
                    @endif
                </form>

                <div class="table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th scope="col">Order</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Items</th>
                                <th scope="col">Delivery Address</th>
                                <th scope="col">Total</th>
                                <th scope="col">Tracking</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr data-order-row="{{ $order->id }}">
                                    <td>
                                        <span class="order-id">{{ $order->order_reference }}</span>
                                        <span class="order-date">{{ $order->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->contact_number }}</td>
                                    <td>{{ $order->items->sum('quantity') ?: 'Not captured' }}</td>
                                    <td>{{ $order->delivery_address }}</td>
                                    <td>₱{{ number_format($order->total ?: $order->subtotal, 2) }}</td>
                                    <td data-tracking-cell>{{ $order->tracking_number ?? 'Awaiting tracking' }}</td>
                                    <td>
                                        <span class="status-pill status-pill--{{ $order->status }}" data-status-pill>{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="action-group" data-update-url="{{ route('admin.orders.update-status', $order) }}">
                                            <a class="status-action status-action--link" href="{{ route('admin.orders.show', $order) }}">View</a>
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
                                    <td class="empty-state" colspan="9">No orders found for this view.</td>
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
