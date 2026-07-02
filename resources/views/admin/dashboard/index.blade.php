<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eluze Admin | Dashboard</title>
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['public/css/admin/orders.css', 'public/js/admin.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}">
        <script src="{{ asset('js/admin.js') }}" defer></script>
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
                <a class="admin-nav__link admin-nav__link--active" href="{{ route('admin.dashboard') }}" aria-label="Dashboard">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M3 11.5 12 4l9 7.5"></path>
                        <path d="M5 10.5V20h14v-9.5"></path>
                        <path d="M9 20v-6h6v6"></path>
                    </svg>
                    <span class="admin-nav__text">Dashboard</span>
                </a>
                <a class="admin-nav__link" href="{{ route('admin.orders.index') }}" aria-label="Orders">
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
                    <h1>Welcome Back</h1>
                </div>
                <div class="admin-header__meta">
                    <span>{{ now()->format('M d, Y') }}</span>
                </div>
            </header>

            <section class="metric-grid" aria-label="Order summary">
                <article class="metric-card">
                    <p>Total Orders</p>
                    <strong>{{ $totalOrders }}</strong>
                </article>
                <article class="metric-card">
                    <p>Pending</p>
                    <strong>{{ $pendingOrders }}</strong>
                </article>
                <article class="metric-card">
                    <p>Processing</p>
                    <strong>{{ $processingOrders }}</strong>
                </article>
                <article class="metric-card">
                    <p>Delivered</p>
                    <strong>{{ $deliveredOrders }}</strong>
                </article>
                <article class="metric-card">
                    <p>New Today</p>
                    <strong>{{ $todayOrders }}</strong>
                </article>
            </section>

            <section class="orders-panel dashboard-panel" aria-labelledby="recent-orders-heading">
                <div class="panel-heading">
                    <h2 id="recent-orders-heading">Recent Orders</h2>
                    <a class="panel-link" href="{{ route('admin.orders.index') }}">View all orders</a>
                </div>

                <div class="recent-list">
                    @forelse ($recentOrders as $order)
                        <article class="recent-order">
                            <div>
                                <span class="order-id">#{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <p>{{ $order->customer_name }}</p>
                            </div>
                            <span class="status-pill status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </article>
                    @empty
                        <p class="empty-state">No recent orders yet.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</body>
</html>
