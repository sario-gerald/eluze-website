<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
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

    <main
        class="cart-page"
        data-order-url="<?= route('orders.store') ?>"
        data-authenticated="<?= auth()->check() && ! auth()->user()->is_admin ? 'true' : 'false' ?>"
    >
        <div class="cart-toolbar" id="cart-toolbar">
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

        <form class="details-form" id="details-form" hidden>
            <div class="checkout-toolbar">
                <button type="button" class="back-button" data-view-cart aria-label="Return to cart">&#8249;</button>
                <h1>Your Details and Address</h1>
            </div>

            <div class="checkout-panel">
                <section class="form-section">
                    <h2>Personal Details</h2>
                    <div class="form-grid form-grid--two">
                        <input type="text" name="surname" placeholder="Surname" autocomplete="family-name" required>
                        <input type="text" name="firstName" placeholder="First Name" autocomplete="given-name" required>
                        <input type="tel" name="contact" placeholder="Contact: 000-000-0000" autocomplete="tel" required>
                        <input type="email" name="email" placeholder="Emailaddress" autocomplete="email" required>
                    </div>
                </section>

                <section class="form-section">
                    <h2>Delivery Address</h2>
                    <div class="form-grid">
                        <select name="region" required>
                            <option value="">Region</option>
                        </select>
                        <select name="city" required>
                            <option value="">City / Municipality</option>
                        </select>
                        <select name="barangay" required>
                            <option value="">Barangay</option>
                        </select>
                        <input type="text" name="street" placeholder="Street" autocomplete="street-address" required>
                        <input type="text" name="landmark" placeholder="Nearest Landmark (Optional)">
                    </div>
                </section>

                <button type="submit" class="save-details">SAVE</button>
            </div>
        </form>

        <section class="order-checkout" id="order-checkout" hidden>
            <div class="checkout-toolbar">
                <button type="button" class="back-button" data-edit-details aria-label="Edit details">&#8249;</button>
                <h1>Go to Cart</h1>
            </div>

            <div class="checkout-panel">
                <button type="button" class="address-summary" data-edit-details>
                    <span class="address-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8c.8-4 3.4-6 7-6s6.2 2 7 6H5Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>Your Personal Details &amp; Address</span>
                    <span aria-hidden="true">&#8250;</span>
                </button>

                <section class="checkout-items" id="checkout-items"></section>
                <p class="delivery-note" id="delivery-note">Calculating delivery date...</p>

                <section class="payment-section">
                    <h2>Payment Method</h2>
                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="GCash" checked>
                        <span class="payment-logo payment-logo--gcash">G</span>
                        <span>GCash</span>
                        <span aria-hidden="true">&#8250;</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="Mastercard">
                        <span class="payment-logo payment-logo--card"></span>
                        <span>Mastercard</span>
                        <span aria-hidden="true">&#8250;</span>
                    </label>
                </section>

                <section class="order-summary">
                    <h2>Order Summary</h2>
                    <dl>
                        <div>
                            <dt>Product subtotal</dt>
                            <dd id="summary-subtotal">&#8369;0.00</dd>
                        </div>
                        <div>
                            <dt>Shipping fee</dt>
                            <dd id="summary-shipping">&#8369;32.00</dd>
                        </div>
                        <div class="summary-total">
                            <dt>Total</dt>
                            <dd id="summary-total">&#8369;0.00</dd>
                        </div>
                    </dl>
                    <label class="terms-check">
                        <input type="checkbox" id="terms-check" required>
                        <span class="square-check"></span>
                        <span>By placing an order, you agree to the <strong>Terms of Use</strong> and <strong>Privacy Policy</strong>. You acknowledge that you have read the service conditions.</span>
                    </label>
                </section>

                <button type="button" class="place-order" id="place-order">PLACE ORDER</button>
                <p class="order-feedback" id="order-feedback" aria-live="polite"></p>
            </div>
        </section>

        <section class="thank-you-view" id="thank-you-view" hidden>
            <div class="checkout-toolbar">
                <a href="/" class="back-button" aria-label="Back to home">&#8249;</a>
                <h1>Your Details and Address</h1>
            </div>

            <div class="thank-you-scene">
                <span class="thank-star thank-star--one" aria-hidden="true">☆</span>
                <span class="thank-star thank-star--two" aria-hidden="true">✦</span>
                <span class="thank-star thank-star--three" aria-hidden="true">☆</span>
                <span class="thank-star thank-star--four" aria-hidden="true">✦</span>
                <img src="<?= asset('images/thank_you_for_purchasing.png') ?>" alt="Thank you for purchasing">
                <a class="back-home-button" href="/">BACK TO HOME</a>
            </div>
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

    <template id="checkout-item-template">
        <article class="checkout-item">
            <div class="checkout-item__image">
                <img src="<?= asset('images/perfume_icon.png') ?>" alt="">
            </div>
            <div class="checkout-item__details">
                <h2></h2>
                <p></p>
                <strong></strong>
            </div>
            <div class="item-controls">
                <button type="button" data-checkout-action="decrease" aria-label="Decrease quantity">-</button>
                <output>1</output>
                <button type="button" data-checkout-action="increase" aria-label="Increase quantity">+</button>
            </div>
        </article>
    </template>

    <script type="application/json" id="current-customer-profile">
        <?= json_encode(auth()->check() ? [
            'surname' => auth()->user()->surname,
            'firstName' => auth()->user()->first_name ?: auth()->user()->name,
            'contact' => auth()->user()->contact_number,
            'email' => auth()->user()->email,
            'region' => auth()->user()->region,
            'city' => auth()->user()->city,
            'barangay' => auth()->user()->barangay,
            'street' => auth()->user()->street,
            'landmark' => auth()->user()->landmark,
        ] : null) ?>
    </script>
    <script src="<?= asset('js/shoppingcart.js') ?>?v=<?= filemtime(public_path('js/shoppingcart.js')) ?>"></script>
</body>
</html>
