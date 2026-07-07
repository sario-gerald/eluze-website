<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eluze Admin | {{ $mode === 'create' ? 'Add Product' : 'Edit Product' }}</title>
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
                <h1>{{ $mode === 'create' ? 'Add Product' : 'Edit Product' }}</h1>
            </div>
            <a class="button button--ghost" href="{{ route('admin.products.index') }}">Back to Products</a>
        </header>

        <section class="orders-panel">
            <div class="panel-heading">
                <h2>Product Details</h2>
                <p>Manage prices, visibility, and stock.</p>
            </div>
            <form class="admin-form" method="POST" action="{{ $mode === 'create' ? route('admin.products.store') : route('admin.products.update', $product) }}">
                @csrf
                @if ($mode === 'edit')
                    @method('PUT')
                @endif
                @if ($errors->any())
                    <p class="form-error">{{ $errors->first() }}</p>
                @endif
                <label>Product Name <input name="name" value="{{ old('name', $product->name) }}" required></label>
                <label>Collection
                    <select name="collection" required>
                        @foreach ($collections as $key => $label)
                            <option value="{{ $key }}" @selected(old('collection', $product->collection) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Scent Notes <input name="scent" value="{{ old('scent', $product->scent) }}"></label>
                <label>Inspiration <input name="inspiration" value="{{ old('inspiration', $product->inspiration) }}"></label>
                <div class="form-grid">
                    <label>10ml Price <input type="number" min="1" name="price_10ml" value="{{ old('price_10ml', $product->price_10ml) }}" required></label>
                    <label>30ml Price <input type="number" min="1" name="price_30ml" value="{{ old('price_30ml', $product->price_30ml) }}" required></label>
                    <label>50ml Price <input type="number" min="1" name="price_50ml" value="{{ old('price_50ml', $product->price_50ml) }}" required></label>
                    <label>75ml Price <input type="number" min="1" name="price_75ml" value="{{ old('price_75ml', $product->price_75ml) }}" required></label>
                    <label>100ml Price <input type="number" min="1" name="price_100ml" value="{{ old('price_100ml', $product->price_100ml) }}" required></label>
                    <label>Stock <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" required></label>
                    <label>Low Stock Alert <input type="number" min="0" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" required></label>
                </div>
                <label class="checkbox-field"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Show this product on the store</label>
                <div class="modal__actions">
                    <a class="button button--ghost" href="{{ route('admin.products.index') }}">Cancel</a>
                    <button class="button button--primary" type="submit">{{ $mode === 'create' ? 'Create Product' : 'Save Changes' }}</button>
                </div>
            </form>
        </section>
    </main>
    </div>
</body>
</html>
