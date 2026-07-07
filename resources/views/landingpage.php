<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Eluze</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/landing.css') ?>?v=<?= filemtime(public_path('css/landing.css')) ?>">
</head>
<body
    data-authenticated="<?= auth()->check() ? 'true' : 'false' ?>"
    data-csrf-url="/customer/csrf-token"
    data-customer-login-url="/customer/login"
    data-customer-register-url="/customer/register"
    data-customer-logout-url="/customer/logout"
>
<body>
    <header class="site-header">
        <a class="brand" href="/" aria-label="Eluze home">
            <img src="<?= asset('images/eluze_logo.png') ?>" alt="Eluze">
        </a>

        <nav class="main-nav" aria-label="Primary navigation">
            <a href="#womens">Collection</a>
            <a href="#story">Our Story</a>
        </nav>

        <div class="header-actions">
            <?php if (auth()->check()): ?>
                <div class="account-menu" id="account-menu">
                    <button class="account-greeting" type="button" id="account-menu-button" aria-expanded="false" aria-controls="account-menu-panel">
                        <span>Hi, <?= e(auth()->user()->first_name ?: auth()->user()->name) ?></span>
                        <span class="account-caret" aria-hidden="true"></span>
                    </button>
                    <div class="account-menu__panel" id="account-menu-panel">
                        <a href="<?= route('customer.orders') ?>">My Orders</a>
                        <button class="account-button account-button--logout" type="button" id="logout-button">Logout</button>
                    </div>
                </div>
            <?php else: ?>
                <button class="account-button" type="button" data-open-auth>Login</button>
            <?php endif; ?>
            <a class="cart-link" href="<?= route('shopping-cart') ?>" aria-label="View shopping cart">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7.2 18.8a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9.8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM4 3.3h2.2l2 10.4a2 2 0 0 0 2 1.6h6.8a2 2 0 0 0 1.9-1.4l1.7-5.6H8.1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <a class="cart-link" href="<?= route('shopping-cart') ?>" aria-label="View shopping cart">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7.2 18.8a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9.8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM4 3.3h2.2l2 10.4a2 2 0 0 0 2 1.6h6.8a2 2 0 0 0 1.9-1.4l1.7-5.6H8.1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </header>

    <main>
        <section class="hero" id="collection">
            <div class="hero-inner">
                <div class="hero-copy">
                    <h1>Elevate Your<br>Everyday <span>Essence</span></h1>
                    <p>Welcome to Eluze. Immerse yourself in a world of pure luxury and discover your signature scent.</p>
                    <a class="shop-button" href="#womens">Shop Now</a>
                </div>

                <div class="hero-visual" aria-hidden="true">
                    <img src="<?= asset('images/eluze_perfume.png') ?>" alt="">
                </div>

                <div class="seller-panel" aria-label="Best seller products">
                    <div class="seller-title">BEST<br>SELLER</div>
                    <?php foreach (($productsByCollection['women'] ?? collect())->take(3) as $index => $product): ?>
                        <article
                            class="seller-item perfume-trigger<?= $product->stock <= 0 ? ' product-card--sold-out' : '' ?>"
                            data-product-id="<?= e($product->id) ?>"
                            data-name="<?= e($product->name) ?>"
                            data-scent="<?= e($product->scent) ?>"
                            data-inspiration="<?= e($product->inspiration) ?>"
                            data-stock="<?= e($product->stock) ?>"
                            data-price-10="<?= e($product->price_10ml) ?>"
                            data-price-30="<?= e($product->price_30ml) ?>"
                            data-price-50="<?= e($product->price_50ml) ?>"
                            data-price-75="<?= e($product->price_75ml) ?>"
                            data-price-100="<?= e($product->price_100ml) ?>"
                            tabindex="0"
                            role="button"
                            aria-label="Order <?= e(ucwords(strtolower($product->name))) ?> perfume"
                        >
                            <img class="bottle-icon" src="<?= asset('images/perfume_icon.png') ?>" alt="">
                            <p>Eluze | <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></p>
                        </article>
                    <?php endforeach; ?>
                    <article class="seller-item perfume-trigger" data-name="DAYDREAM" data-scent="sweet, airy, cloudlike" tabindex="0" role="button" aria-label="Order Daydream perfume">
                        <img class="bottle-icon" src="<?= asset('images/perfume_icon.png') ?>" alt="">
                        <p>Eluze | 01</p>
                    </article>
                    <article class="seller-item perfume-trigger" data-name="LIORA" data-scent="floral, playful, delicate" tabindex="0" role="button" aria-label="Order Liora perfume">
                        <img class="bottle-icon" src="<?= asset('images/perfume_icon.png') ?>" alt="">
                        <p>Eluze | 02</p>
                    </article>
                    <article class="seller-item perfume-trigger" data-name="SOLENNE" data-scent="elegant, soft, musky" tabindex="0" role="button" aria-label="Order Solenne perfume">
                        <img class="bottle-icon" src="<?= asset('images/perfume_icon.png') ?>" alt="">
                        <p>Eluze | 03</p>
                    </article>
                </div>
            </div>
            <span class="scroll-mark" aria-hidden="true"></span>
        </section>

        <section class="story" id="story">
            <div class="story-band" aria-hidden="true"></div>
            <div class="section-heading">
                <span></span>
                <h2>Our Story</h2>
                <span></span>
            </div>

            <div class="story-cards">
                <article class="story-card">
                    <img class="story-icon" src="<?= asset('images/inspiration_icon.png') ?>" alt="">
                    <h3>The Inspiration</h3>
                    <p>Rooted in the delicate balance between modern sophistication and timeless elegance. Every drop tells a story of pure luxury.</p>
                </article>
                <article class="story-card">
                    <img class="story-icon" src="<?= asset('images/ingredients_icon.png') ?>" alt="">
                    <h3>The Ingredients</h3>
                    <p>Utilizing only the finest elements to create an aura of pure luxury. We source our notes carefully to ensure a captivating profile.</p>
                </article>
                <article class="story-card">
                    <img class="story-icon" src="<?= asset('images/craft_icon.png') ?>" alt="">
                    <h3>The Craft</h3>
                    <p>Meticulously blended to capture memories, evoke emotion, and leave a timeless impression wherever you go.</p>
                </article>
            </div>
        </section>

        <section class="collection-section women-collection" id="womens">
            <div class="collection-heading">
                <span></span>
                <h2><strong>Women's</strong> Collection</h2>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3.2c5.3 0 8.8 3.8 8.8 8.9 0 5.2-3.6 8.7-8.8 8.7s-8.8-3.5-8.8-8.7c0-5.1 3.5-8.9 8.8-8.9Z" fill="currentColor"/>
                    <path d="M8.3 12.2c1.2 0 2.1-.6 2.7-1.6 1.2 1.5 2.7 2.2 4.7 2.3-.2 2.4-1.8 4-3.8 4-2.2 0-3.8-1.8-3.8-4.3v-.4h.2Z" fill="#fff"/>
                    <path d="M8.2 9.4c1.2-1.7 2.5-2.6 4.1-2.6 1.8 0 3.2 1 4 3.1-2.5-.1-4.2-.8-5.4-2.2-.5 1-1.4 1.6-2.7 1.7Z" fill="#fff"/>
                </svg>
                <span></span>
            </div>
            <p class="collection-subtitle">Select Your Desired Perfume to Purchase</p>

            <div class="product-grid women-grid">
                <?php foreach (($productsByCollection['women'] ?? collect()) as $product): ?>
                    <article
                        class="product-card<?= $product->stock <= 0 ? ' product-card--sold-out' : '' ?>"
                        data-product-id="<?= e($product->id) ?>"
                        data-name="<?= e($product->name) ?>"
                        data-scent="<?= e($product->scent) ?>"
                        data-inspiration="<?= e($product->inspiration) ?>"
                        data-stock="<?= e($product->stock) ?>"
                        data-price-10="<?= e($product->price_10ml) ?>"
                        data-price-30="<?= e($product->price_30ml) ?>"
                        data-price-50="<?= e($product->price_50ml) ?>"
                        data-price-75="<?= e($product->price_75ml) ?>"
                        data-price-100="<?= e($product->price_100ml) ?>"
                    >
                        <span class="product-bottle"><span>ELUZE</span></span>
                        <h3><?= e($product->name) ?></h3>
                        <p><?= e($product->scent) ?></p>
                        <?php if ($product->stock <= 0): ?>
                            <span class="stock-note">Out of stock</span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>DAYDREAM</h3>
                    <p>sweet, airy, cloudlike</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>LIORA</h3>
                    <p>floral, playful, delicate</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>SOLENNE</h3>
                    <p>elegant, soft, musky</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>LILAC CREST</h3>
                    <p>floral, powdery, mysterious</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>AFTERLIGHT</h3>
                    <p>sweet, airy, cloudlike</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>VELVET</h3>
                    <p>warm, spicy, seductive</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>LUMIERE</h3>
                    <p>delicate, crisp, pear-like</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>BLUSHINE</h3>
                    <p>soft, powdery feminine</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>DUSK SERENADE</h3>
                    <p>warm, vanilla, romantic</p>
                </article>
                <article class="product-card product-card-centered">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>SAFFRON HORIZON</h3>
                    <p>golden, warm, radiant</p>
                </article>
            </div>
        </section>

        <section class="collection-section men-collection" id="mens">
            <div class="collection-heading">
                <span></span>
                <h2><strong>Men's</strong> Collection</h2>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3.4c5 0 8.8 3.8 8.8 8.6 0 5.1-3.7 8.6-8.8 8.6S3.2 17.1 3.2 12c0-4.8 3.8-8.6 8.8-8.6Z" fill="currentColor"/>
                    <path d="M7.7 10.2c1.7-.5 3-1.4 3.8-2.8 1 1.7 2.8 2.8 5.1 3.2v1.8c0 2.8-1.8 4.6-4.5 4.6s-4.5-1.8-4.5-4.6v-2.2Z" fill="#fff"/>
                    <path d="M9.4 12.4h1.1m3.1 0h1.1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                </svg>
                <span></span>
            </div>
            <p class="collection-subtitle">Select Your Desired Perfume to Purchase</p>

            <div class="product-grid men-grid">
                <?php foreach (($productsByCollection['men'] ?? collect()) as $product): ?>
                    <article
                        class="product-card<?= $product->stock <= 0 ? ' product-card--sold-out' : '' ?>"
                        data-product-id="<?= e($product->id) ?>"
                        data-name="<?= e($product->name) ?>"
                        data-scent="<?= e($product->scent) ?>"
                        data-inspiration="<?= e($product->inspiration) ?>"
                        data-stock="<?= e($product->stock) ?>"
                        data-price-10="<?= e($product->price_10ml) ?>"
                        data-price-30="<?= e($product->price_30ml) ?>"
                        data-price-50="<?= e($product->price_50ml) ?>"
                        data-price-75="<?= e($product->price_75ml) ?>"
                        data-price-100="<?= e($product->price_100ml) ?>"
                    >
                        <span class="product-bottle"><span>ELUZE</span></span>
                        <h3><?= e($product->name) ?></h3>
                        <p><?= e($product->scent) ?></p>
                        <?php if ($product->stock <= 0): ?>
                            <span class="stock-note">Out of stock</span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>MIDNIGHT ARC</h3>
                    <p>smoky, woody, intense</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>ZEPHYR</h3>
                    <p>fresh, airy, breezy</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>SCARLET WHISPER</h3>
                    <p>fruity, vibrant, romantic</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>OCEAN VERSE</h3>
                    <p>fresh, aquatic, clean</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>VALEUR</h3>
                    <p>fresh, bold, aromatic</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>COBALT EMBER</h3>
                    <p>spicy, bold, invigorating</p>
                </article>
            </div>
        </section>

        <section class="collection-section unisex-collection" id="unisex">
            <div class="collection-heading">
                <span></span>
                <h2><strong>Unisex</strong> Collection</h2>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 4.2a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Zm-6.2 15.1c.4-3.7 2.8-6 6.2-6s5.8 2.3 6.2 6H5.8Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                <span></span>
            </div>
            <p class="collection-subtitle">Select Your Desired Perfume to Purchase</p>

            <div class="product-grid unisex-grid">
                <?php foreach (($productsByCollection['unisex'] ?? collect()) as $product): ?>
                    <article
                        class="product-card<?= $product->stock <= 0 ? ' product-card--sold-out' : '' ?>"
                        data-product-id="<?= e($product->id) ?>"
                        data-name="<?= e($product->name) ?>"
                        data-scent="<?= e($product->scent) ?>"
                        data-inspiration="<?= e($product->inspiration) ?>"
                        data-stock="<?= e($product->stock) ?>"
                        data-price-10="<?= e($product->price_10ml) ?>"
                        data-price-30="<?= e($product->price_30ml) ?>"
                        data-price-50="<?= e($product->price_50ml) ?>"
                        data-price-75="<?= e($product->price_75ml) ?>"
                        data-price-100="<?= e($product->price_100ml) ?>"
                    >
                        <span class="product-bottle"><span>ELUZE</span></span>
                        <h3><?= e($product->name) ?></h3>
                        <p><?= e($product->scent) ?></p>
                        <?php if ($product->stock <= 0): ?>
                            <span class="stock-note">Out of stock</span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>CRIMSON VEIL</h3>
                    <p>warm, amber, spicy</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>SLYVAN</h3>
                    <p>woody, warm, sensual</p>
                </article>
                <article class="product-card">
                    <span class="product-bottle"><span>ELUZE</span></span>
                    <h3>APRICOT MUSE</h3>
                    <p>sweet, fruity, floral</p>
                </article>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <section class="footer-brand">
                <h2>ELUZE<br><span>エルズ</span></h2>
                <p>Elevating your everyday essence through meticulously crafted, timeless fragrances. Designed to leave a lasting impression.</p>
            </section>

            <section>
                <h3>The Collection</h3>
                <a href="#mens">Men's Fragrances</a>
                <a href="#womens">Women's Fragrances</a>
                <a href="#unisex">Unisex Blend</a>
                <a href="#womens">Travel Sizes</a>
            </section>

            <section>
                <h3>Client Care</h3>
                <a href="#">Contact Us</a>
                <a href="#">Shipping &amp; Returns</a>
                <a href="#">FAQ</a>
                <a href="#">Track Order</a>
            </section>

            <section class="footer-connect">
                <h3>Stay Connected</h3>
                <p>Follow us to receive exclusive offers and news of our latest creations.</p>
                <div class="footer-rule"></div>
                <div class="social-links" aria-label="Social links">
                    <a href="https://www.facebook.com/profile.php?id=100072231574264" target="_blank" rel="noopener noreferrer" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="5" width="14" height="14" rx="4" fill="none" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="12" r="3.2" fill="none" stroke="currentColor" stroke-width="2"/>
                            <circle cx="16.5" cy="7.5" r="1" fill="currentColor"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Email">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 7h16v10H4V7Zm0 0 8 6 8-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </section>
        </div>
        <p class="copyright">&copy; 2026 Eluze Fragrances. All rights reserved.</p>
    </footer>

    <div class="order-modal" id="order-modal" hidden>
        <button class="order-modal__backdrop" type="button" aria-label="Close order options" data-close-modal></button>
        <section class="order-dialog" role="dialog" aria-modal="true" aria-labelledby="order-product-name">
            <button class="order-dialog__close" type="button" aria-label="Close order options" data-close-modal>&times;</button>

            <div class="order-product">
                <span class="order-bottle"><span>ELUZE</span></span>
                <div class="order-product__details">
                    <h2 id="order-product-name">DAYDREAM</h2>
                    <p id="order-product-scent">sweet, airy, cloudlike</p>
                    <p class="order-product__inspiration">Inspired by: <span id="order-product-inspired">morning light and soft clouds</span></p>
                </div>
            </div>

            <div class="order-sizes" role="group" aria-label="Select perfume size">
                <button type="button" data-size="10" data-price="850" class="is-selected">10 ML</button>
                <button type="button" data-size="30" data-price="1350">30 ML</button>
                <button type="button" data-size="50" data-price="1850">50 ML</button>
                <button type="button" data-size="75" data-price="2350">75 ML</button>
                <button type="button" data-size="100" data-price="2850">100 ML</button>
            </div>

            <div class="order-summary">
                <div class="quantity-control" aria-label="Quantity selector">
                    <button type="button" data-quantity-action="decrease" aria-label="Decrease quantity">-</button>
                    <output id="order-quantity" aria-live="polite">1</output>
                    <button type="button" data-quantity-action="increase" aria-label="Increase quantity">+</button>
                </div>
                <strong id="order-price">&#8369;850</strong>
            </div>

            <div class="order-actions">
                <button type="button" class="order-action order-action--secondary" id="add-to-cart">ADD TO CART</button>
                <button type="button" class="order-action order-action--primary" id="buy-now">BUY NOW</button>
            </div>
            <p class="order-feedback" id="order-feedback" aria-live="polite"></p>
        </section>
    </div>

    <div class="auth-modal" id="auth-modal" hidden>
        <button class="auth-modal__backdrop" type="button" aria-label="Close login and register" data-close-auth></button>
        <section class="auth-dialog" role="dialog" aria-modal="true" aria-labelledby="auth-title">
            <button class="auth-dialog__close" type="button" aria-label="Close login and register" data-close-auth>&times;</button>
            <div class="auth-tabs" role="tablist" aria-label="Account options">
                <button type="button" class="is-active" data-auth-tab="login">Login</button>
                <button type="button" data-auth-tab="register">Register</button>
            </div>

            <h2 id="auth-title">Welcome to Eluze</h2>
            <p class="auth-intro" id="auth-intro">Sign in to autofill checkout details when you order.</p>

            <form class="auth-form" id="login-form" data-auth-panel="login">
                <input type="email" name="email" placeholder="Email address" autocomplete="email" required>
                <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>
                <button type="submit">LOGIN</button>
            </form>

            <form class="auth-form auth-form--register" id="register-form" data-auth-panel="register" hidden>
                <div class="auth-grid">
                    <input type="text" name="surname" placeholder="Surname" autocomplete="family-name" required>
                    <input type="text" name="firstName" placeholder="First Name" autocomplete="given-name" required>
                    <input type="tel" name="contact" placeholder="Contact number" autocomplete="tel" required>
                    <input type="email" name="email" placeholder="Email address" autocomplete="email" required>
                    <input type="password" name="password" placeholder="Password" autocomplete="new-password" required>
                </div>
                <p class="auth-section-label">Delivery details for faster checkout</p>
                <div class="auth-grid auth-grid--address">
                    <select name="region">
                        <option value="">Region</option>
                    </select>
                    <select name="city">
                        <option value="">City / Municipality</option>
                    </select>
                    <select name="barangay">
                        <option value="">Barangay</option>
                    </select>
                    <input type="text" name="street" placeholder="Street" autocomplete="street-address">
                    <input type="text" name="landmark" placeholder="Nearest Landmark (Optional)">
                </div>
                <button type="submit">REGISTER</button>
            </form>

            <p class="auth-feedback" id="auth-feedback" aria-live="polite"></p>
        </section>
    </div>

    <script src="<?= asset('js/landing.js') ?>?v=<?= filemtime(public_path('js/landing.js')) ?>"></script>
</body>
</html>
