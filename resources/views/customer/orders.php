<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Eluze</title>
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
            <a href="/#story">Our Story</a>
            <a href="<?= route('shopping-cart') ?>">Cart</a>
        </nav>
    </header>

    <main class="orders-page">
        <div class="orders-toolbar">
            <a href="/" class="back-link" aria-label="Back to home">&#8249;</a>
            <div>
                <p>Hi, <?= e($user->first_name ?: $user->name) ?></p>
                <h1>My Orders</h1>
            </div>
        </div>

        <?php if ($orders->isEmpty()): ?>
            <section class="orders-empty">
                <h2>No orders yet</h2>
                <p>Your order history and delivery status will appear here after checkout.</p>
                <a href="/#womens">SHOP COLLECTION</a>
            </section>
        <?php else: ?>
            <section class="orders-list" aria-label="Customer orders">
                <?php foreach ($orders as $order): ?>
                    <article class="order-card">
                        <div class="order-card__top">
                            <div>
                                <p>Order reference</p>
                                <h2><?= e($order->order_reference) ?></h2>
                            </div>
                            <div class="order-status">
                                <span class="status-pill status-pill--<?= e($order->status) ?>"><?= e(ucfirst($order->status)) ?></span>
                                <small>
                                    <?php if ($order->status === 'pending'): ?>
                                        Waiting for confirmation
                                    <?php elseif ($order->status === 'processing'): ?>
                                        Preparing your order
                                    <?php else: ?>
                                        Order completed
                                    <?php endif; ?>
                                </small>
                                <a class="view-order-link" href="<?= route('customer.orders.show', ['reference' => $order->order_reference]) ?>">VIEW</a>
                            </div>
                        </div>

                        <dl class="order-details">
                            <div>
                                <dt>Date placed</dt>
                                <dd><?= e($order->created_at->format('F d, Y')) ?></dd>
                            </div>
                            <div>
                                <dt>Contact</dt>
                                <dd><?= e($order->contact_number) ?></dd>
                            </div>
                            <div>
                                <dt>Delivery address</dt>
                                <dd><?= e($order->delivery_address) ?></dd>
                            </div>
                            <div>
                                <dt>Tracking number</dt>
                                <dd><?= e($order->tracking_number ?: 'Awaiting tracking') ?></dd>
                            </div>
                        </dl>
                    </article>
                <?php endforeach; ?>
            </section>

            <div class="orders-pagination">
                <?= $orders->links() ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
