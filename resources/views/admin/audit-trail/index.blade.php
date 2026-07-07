<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eluze Admin | Audit Trail</title>
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
                <a class="admin-nav__link" href="{{ route('admin.products.index') }}" aria-label="Products">
                    <svg class="admin-nav__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M6 3h12l1 6H5l1-6Z"></path>
                        <path d="M5 9v12h14V9"></path>
                        <path d="M9 13h6"></path>
                    </svg>
                    <span class="admin-nav__text">Products</span>
                </a>
                <a class="admin-nav__link admin-nav__link--active" href="{{ route('admin.audit-trail.index') }}" aria-label="Audit Trail">
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
                    <p class="eyebrow">Admin Security</p>
                    <h1>Audit Trail</h1>
                </div>
                <div class="admin-header__meta">
                    <span>{{ now()->format('M d, Y') }}</span>
                </div>
            </header>

            <section class="status-tabs" aria-label="Filter audit trail by action">
                <a class="status-tab {{ $activeAction === null ? 'status-tab--active' : '' }}" href="{{ route('admin.audit-trail.index') }}">
                    All
                    <span>{{ $totalLogs }}</span>
                </a>
                @foreach ($actions as $action)
                    <a class="status-tab {{ $activeAction === $action ? 'status-tab--active' : '' }}" href="{{ route('admin.audit-trail.index', ['action' => $action]) }}">
                        {{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $action)) }}
                    </a>
                @endforeach
            </section>

            <section class="orders-panel audit-panel" aria-labelledby="audit-heading">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">System Activity</p>
                        <h2 id="audit-heading">Recent Admin Actions</h2>
                    </div>
                    <p>{{ $logs->total() }} {{ \Illuminate\Support\Str::plural('record', $logs->total()) }}</p>
                </div>

                <div class="audit-list">
                    @forelse ($logs as $log)
                        <article class="audit-item">
                            <div class="audit-item__marker" aria-hidden="true"></div>
                            <div class="audit-item__body">
                                <div class="audit-item__topline">
                                    <span class="audit-action">{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $log->action)) }}</span>
                                    <time datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->format('M d, Y h:i A') }}</time>
                                </div>
                                <h2>{{ $log->description }}</h2>
                                <dl class="audit-meta">
                                    <div>
                                        <dt>Admin</dt>
                                        <dd>{{ $log->user?->name ?? 'Unknown admin' }}</dd>
                                    </div>
                                    <div>
                                        <dt>IP Address</dt>
                                        <dd>{{ $log->ip_address ?? 'Not captured' }}</dd>
                                    </div>
                                    @if (($log->details['order_reference'] ?? null))
                                        <div>
                                            <dt>Order</dt>
                                            <dd>{{ $log->details['order_reference'] }}</dd>
                                        </div>
                                    @endif
                                    @if (($log->details['previous_status'] ?? null) && ($log->details['new_status'] ?? null))
                                        <div>
                                            <dt>Status Change</dt>
                                            <dd>{{ ucfirst($log->details['previous_status']) }} to {{ ucfirst($log->details['new_status']) }}</dd>
                                        </div>
                                    @endif
                                    @if (($log->details['new_tracking_number'] ?? null))
                                        <div>
                                            <dt>Tracking</dt>
                                            <dd>{{ $log->details['new_tracking_number'] }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </article>
                    @empty
                        <p class="empty-state">No audit activity has been recorded yet.</p>
                    @endforelse
                </div>

                <div class="pagination-wrap">
                    {{ $logs->links() }}
                </div>
            </section>
        </main>
    </div>
</body>
</html>
