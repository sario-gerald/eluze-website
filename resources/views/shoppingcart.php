<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Eluze</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/shoppingcart.css') ?>?v=<?= filemtime(public_path('css/shoppingcart.css')) ?>">
</head>
<body>
    <header class="cart-header">
        <a class="cart-brand" href="/" aria-label="Eluze home">
            <img src="<?= asset('images/eluze_logo.png') ?>" alt="Eluze">
        </a>
        <nav aria-label="Primary navigation">
            <a href="/#womens">Collection</a>
            <a href="/#story">Our Story</a>
        </nav>
    </header>

    <main class="cart-page">
        <div class="cart-toolbar">
            <a href="/" class="back-link" aria-label="Return to shop">&#8249;</a>
            <h1>Shopping Cart</h1>
            <button type="button" id="edit-cart">Edit</button>
        </div>

        <section class="cart-list" id="cart-list" aria-label="Shopping cart items"></section>

        <section class="empty-cart" id="empty-cart" hidden>
            <h2>Your cart is empty</h2>
            <p>Choose a fragrance from the Eluze collection.</p>
            <a href="/#womens">SHOP COLLECTION</a>
        </section>
    </main>

    <footer class="checkout-bar" id="checkout-bar">
        <label class="select-all">
            <input type="checkbox" id="select-all">
            <span class="checkmark"></span>
            All
        </label>
        <div class="checkout-total">
            <strong id="cart-total">&#8369;0</strong>
            <button type="button" id="checkout-button">CHECKOUT</button>
        </div>
        <p id="checkout-feedback" aria-live="polite"></p>
    </footer>

    <template id="cart-item-template">
        <article class="cart-item">
            <label class="item-select">
                <input type="checkbox" class="item-checkbox">
                <span class="checkmark"></span>
            </label>
            <div class="item-image">
                <img src="<?= asset('images/perfume_icon.png') ?>" alt="">
            </div>
            <div class="item-details">
                <h2></h2>
                <p class="item-variant"></p>
                <strong class="item-price"></strong>
            </div>
            <div class="item-controls">
                <button type="button" data-action="decrease" aria-label="Decrease quantity">-</button>
                <output>1</output>
                <button type="button" data-action="increase" aria-label="Increase quantity">+</button>
            </div>
            <button class="remove-item" type="button" aria-label="Remove item">Remove</button>
        </article>
    </template>

    <script src="<?= asset('js/shoppingcart.js') ?>?v=<?= filemtime(public_path('js/shoppingcart.js')) ?>"></script>
</body>
</html>
