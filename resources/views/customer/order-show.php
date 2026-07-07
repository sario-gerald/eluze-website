<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking | Eluze</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/customer-orders.css') ?>?v=<?= filemtime(public_path('css/customer-orders.css')) ?>">
</head>
<body>
    <header class="orders-header">
        <a class="orders-brand" href="/" aria-label="Eluze home">
            <img src="<?= asset('images/eluze_logo.png') ?>" alt="Eluze">
        </a>
        <nav aria-label="Primary navigation">
            <a href="/#womens">Collection</a>
            <a href="<?= route('customer.orders') ?>">My Orders</a>
            <a href="<?= route('shopping-cart') ?>">Cart</a>
        </nav>
    </header>

    <main class="orders-page order-track-page" data-tracking-url="<?= route('customer.orders.tracking', ['reference' => $order->order_reference]) ?>">
        <div class="orders-toolbar">
            <a href="<?= route('customer.orders') ?>" class="back-link" aria-label="Back to my orders">&#8249;</a>
            <div>
                <p>Order tracking</p>
                <h1><?= e($order->order_reference) ?></h1>
            </div>
        </div>

        <section class="tracking-hero">
            <div>
                <p>Current status</p>
                <h2 data-status-label><?= e(ucfirst($order->status)) ?></h2>
                <span data-status-help>
                    <?php if ($order->status === 'pending'): ?>
                        Waiting for confirmation
                    <?php elseif ($order->status === 'processing'): ?>
                        Preparing your order
                    <?php else: ?>
                        Order completed
                    <?php endif; ?>
                </span>
            </div>
            <span class="status-pill status-pill--<?= e($order->status) ?>" data-status-pill><?= e(ucfirst($order->status)) ?></span>
        </section>

        <section class="tracking-card">
            <h2>Live Tracking</h2>
            <ol class="tracking-steps" data-current-status="<?= e($order->status) ?>">
                <li data-step="pending">
                    <span></span>
                    <div>
                        <strong>Order received</strong>
                        <p>Your order was placed and is waiting for confirmation.</p>
                    </div>
                </li>
                <li data-step="processing">
                    <span></span>
                    <div>
                        <strong>Processing</strong>
                        <p>Eluze is preparing your fragrance for delivery.</p>
                    </div>
                </li>
                <li data-step="delivered">
                    <span></span>
                    <div>
                        <strong>Delivered</strong>
                        <p>Your order has been completed.</p>
                    </div>
                </li>
            </ol>
            <p class="tracking-refresh">Last updated: <span data-updated-at><?= e($order->updated_at->format('F d, Y h:i A')) ?></span></p>
        </section>

        <section class="tracking-grid">
            <article class="tracking-info">
                <h2>Order Information</h2>
                <dl class="order-details order-details--stacked">
                    <div>
                        <dt>Reference</dt>
                        <dd><?= e($order->order_reference) ?></dd>
                    </div>
                    <div>
                        <dt>Date placed</dt>
                        <dd><?= e($order->created_at->format('F d, Y')) ?></dd>
                    </div>
                    <div>
                        <dt>Tracking number</dt>
                        <dd data-tracking-number><?= e($order->tracking_number ?: 'Awaiting tracking') ?></dd>
                    </div>
                </dl>
            </article>

            <article class="tracking-info">
                <h2>Customer and Delivery</h2>
                <dl class="order-details order-details--stacked">
                    <div>
                        <dt>Customer</dt>
                        <dd><?= e($order->customer_name) ?></dd>
                    </div>
                    <div>
                        <dt>Contact</dt>
                        <dd><?= e($order->contact_number) ?></dd>
                    </div>
                    <div>
                        <dt>Delivery address</dt>
                        <dd><?= e($order->delivery_address) ?></dd>
                    </div>
                </dl>
            </article>
        </section>
    </main>

    <script src="<?= asset('js/customer-order-tracking.js') ?>?v=<?= filemtime(public_path('js/customer-order-tracking.js')) ?>"></script>
</body>
</html>
