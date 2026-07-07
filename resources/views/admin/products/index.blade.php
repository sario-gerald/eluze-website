<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eluze Admin | Products</title>
    <link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}">
    <script src="{{ asset('js/admin.js') }}" defer></script>
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
                <a class="admin-nav__link" href="{{ route('admin.orders.index') }}" aria-label="Orders">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M7 7h14l-2 9H8L6 4H3"></path>
                        <path d="M9 20h.01"></path>
                        <path d="M18 20h.01"></path>
                    </svg>
                    <span class="admin-nav__text">Orders</span>
                </a>
                <a class="admin-nav__link admin-nav__link--active" href="{{ route('admin.products.index') }}" aria-label="Products">
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
                <p class="eyebrow">Inventory</p>
                <h1>Product Management</h1>
            </div>
            <div class="admin-header-actions">
                <a class="button button--ghost" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="button button--primary" href="{{ route('admin.products.create') }}">Add Product</a>
            </div>
        </header>

        @if (session('status'))
            <p class="admin-flash">{{ session('status') }}</p>
        @endif

        <section class="status-tabs" aria-label="Filter products">
            <a class="status-tab {{ empty($filters['collection']) && empty($filters['stock']) ? 'status-tab--active' : '' }}" href="{{ route('admin.products.index') }}">All</a>
            @foreach ($collections as $key => $label)
                <a class="status-tab {{ ($filters['collection'] ?? null) === $key ? 'status-tab--active' : '' }}" href="{{ route('admin.products.index', ['collection' => $key]) }}">{{ $label }}</a>
            @endforeach
            <a class="status-tab {{ ($filters['stock'] ?? null) === 'low' ? 'status-tab--active' : '' }}" href="{{ route('admin.products.index', ['stock' => 'low']) }}">Low Stock</a>
            <a class="status-tab {{ ($filters['stock'] ?? null) === 'out' ? 'status-tab--active' : '' }}" href="{{ route('admin.products.index', ['stock' => 'out']) }}">Out of Stock</a>
        </section>

        <section class="orders-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">Catalog</p>
                    <h2>Products</h2>
                </div>
                <p>{{ $products->total() }} {{ \Illuminate\Support\Str::plural('record', $products->total()) }}</p>
            </div>

            <form class="admin-filter-form" method="GET" action="{{ route('admin.products.index') }}">
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search product, collection, or scent">
                <button class="button button--primary" type="submit">Search</button>
                @if (! empty($filters))
                    <a class="button button--ghost" href="{{ route('admin.products.index') }}">Clear</a>
                @endif
            </form>

            <div class="table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Collection</th>
                            <th>10ml</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td><span class="order-id">{{ $product->name }}</span><span class="order-date">{{ $product->scent }}</span></td>
                                <td>{{ $product->collection_label }}</td>
                                <td>₱{{ number_format($product->price_10ml, 2) }}</td>
                                <td>{{ $product->stock }} pcs</td>
                                <td><span class="status-pill status-pill--{{ $product->stock_status === 'available' ? 'delivered' : ($product->stock_status === 'low' ? 'pending' : 'processing') }}">{{ $product->is_active ? ucfirst($product->stock_status) : 'Hidden' }}</span></td>
                                <td><a class="status-action status-action--link" href="{{ route('admin.products.edit', $product) }}">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td class="empty-state" colspan="6">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $products->links() }}</div>
        </section>
    </main>
    </div>
</body>
</html>
