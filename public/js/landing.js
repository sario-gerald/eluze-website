document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener("click", (event) => {
        const target = document.querySelector(link.getAttribute("href"));

        if (!target) {
            return;
        }

        event.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
    });
});

const authModal = document.querySelector("#auth-modal");

if (authModal) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    let csrfToken = csrfMeta?.getAttribute("content");
    const loginForm = authModal.querySelector("#login-form");
    const registerForm = authModal.querySelector("#register-form");
    const authTitle = authModal.querySelector("#auth-title");
    const authIntro = authModal.querySelector("#auth-intro");
    const authFeedback = authModal.querySelector("#auth-feedback");
    const authTabs = authModal.querySelectorAll("[data-auth-tab]");
    const authPanels = authModal.querySelectorAll("[data-auth-panel]");
    const regionSelect = registerForm.elements.region;
    const citySelect = registerForm.elements.city;
    const barangaySelect = registerForm.elements.barangay;
    const psgcApi = "https://psgc.gitlab.io/api";
    let authAddressLoaded = false;

    const setSelectMessage = (select, message) => {
        select.replaceChildren(new Option(message, ""));
    };

    const populateSelect = (select, items, placeholder) => {
        select.replaceChildren(new Option(placeholder, ""));
        items
            .slice()
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach((item) => {
                const option = new Option(item.name, item.name);
                option.dataset.code = item.code;
                select.append(option);
            });
    };

    const fetchPsgc = async (path) => {
        const response = await fetch(`${psgcApi}${path}`);

        if (!response.ok) {
            throw new Error("Address list is unavailable right now.");
        }

        return response.json();
    };

    const loadRegions = async () => {
        setSelectMessage(regionSelect, "Loading regions...");
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        setSelectMessage(citySelect, "Choose region first");
        setSelectMessage(barangaySelect, "Choose city first");

        try {
            const regions = await fetchPsgc("/regions/");
            populateSelect(regionSelect, regions, "Region");
            regionSelect.disabled = false;
        } catch (error) {
            setSelectMessage(regionSelect, error.message);
        }
    };

    const loadCities = async () => {
        const regionCode = regionSelect.selectedOptions[0]?.dataset.code;

        barangaySelect.disabled = true;
        setSelectMessage(barangaySelect, "Choose city first");

        if (!regionCode) {
            citySelect.disabled = true;
            setSelectMessage(citySelect, "Choose region first");
            return;
        }

        citySelect.disabled = true;
        setSelectMessage(citySelect, "Loading cities...");

        try {
            const cities = await fetchPsgc(`/regions/${regionCode}/cities-municipalities/`);
            populateSelect(citySelect, cities, "City / Municipality");
            citySelect.disabled = false;
        } catch (error) {
            setSelectMessage(citySelect, error.message);
        }
    };

    const loadBarangays = async () => {
        const cityCode = citySelect.selectedOptions[0]?.dataset.code;

        if (!cityCode) {
            barangaySelect.disabled = true;
            setSelectMessage(barangaySelect, "Choose city first");
            return;
        }

        barangaySelect.disabled = true;
        setSelectMessage(barangaySelect, "Loading barangays...");

        try {
            const barangays = await fetchPsgc(`/cities-municipalities/${cityCode}/barangays/`);
            populateSelect(barangaySelect, barangays, "Barangay");
            barangaySelect.disabled = false;
        } catch (error) {
            setSelectMessage(barangaySelect, error.message);
        }
    };

    const showAuthTab = (tabName) => {
        authTabs.forEach((tab) => tab.classList.toggle("is-active", tab.dataset.authTab === tabName));
        authPanels.forEach((panel) => {
            panel.hidden = panel.dataset.authPanel !== tabName;
        });
        authTitle.textContent = tabName === "login" ? "Login to Eluze" : "Create Your Account";
        authIntro.textContent = tabName === "login"
            ? "Sign in to autofill checkout details when you order."
            : "Save your details once, then edit them anytime during checkout.";
        authModal.classList.toggle("is-registering", tabName === "register");
        authFeedback.textContent = "";

        if (tabName === "register" && !authAddressLoaded) {
            authAddressLoaded = true;
            loadRegions();
        }
    };

    const openAuthModal = () => {
        authModal.hidden = false;
        document.body.classList.add("modal-open");
        showAuthTab("login");
        loginForm.elements.email.focus();
    };

    const closeAuthModal = () => {
        authModal.hidden = true;
        document.body.classList.remove("modal-open");
        sessionStorage.setItem("eluzeAuthModalClosed", "true");
    };

    const refreshCsrfToken = async () => {
        const response = await fetch(document.body.dataset.csrfUrl, {
            headers: {
                Accept: "application/json",
            },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error("Your session expired. Please refresh and try again.");
        }

        const data = await response.json();
        csrfToken = data.token;

        if (csrfMeta) {
            csrfMeta.setAttribute("content", csrfToken);
        }
    };

    const postJson = (url, data) => fetch(url, {
        method: "POST",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        credentials: "same-origin",
        body: JSON.stringify(data),
    });

    const submitAuthForm = async (form, url) => {
        const submitButton = form.querySelector('button[type="submit"]');
        const data = Object.fromEntries(new FormData(form).entries());

        submitButton.disabled = true;
        authFeedback.textContent = "";

        try {
            let response = await postJson(url, data);

            if (response.status === 419) {
                await refreshCsrfToken();
                response = await postJson(url, data);
            }

            const result = await response.json();

            if (!response.ok) {
                const firstError = result.errors
                    ? Object.values(result.errors).flat()[0]
                    : result.message;
                throw new Error(firstError || "Please check your details and try again.");
            }

            authFeedback.textContent = result.message || "Welcome to Eluze.";
            sessionStorage.removeItem("eluzeAuthModalClosed");
            window.setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            authFeedback.textContent = error.message;
        } finally {
            submitButton.disabled = false;
        }
    };

    document.querySelectorAll("[data-open-auth]").forEach((button) => {
        button.addEventListener("click", openAuthModal);
    });

    authModal.querySelectorAll("[data-close-auth]").forEach((button) => {
        button.addEventListener("click", closeAuthModal);
    });

    authTabs.forEach((tab) => {
        tab.addEventListener("click", () => showAuthTab(tab.dataset.authTab));
    });

    regionSelect.addEventListener("change", loadCities);
    citySelect.addEventListener("change", loadBarangays);

    loginForm.addEventListener("submit", (event) => {
        event.preventDefault();
        submitAuthForm(loginForm, document.body.dataset.customerLoginUrl);
    });

    registerForm.addEventListener("submit", (event) => {
        event.preventDefault();
        submitAuthForm(registerForm, document.body.dataset.customerRegisterUrl);
    });

    const accountMenu = document.querySelector("#account-menu");
    const accountMenuButton = document.querySelector("#account-menu-button");

    accountMenuButton?.addEventListener("click", () => {
        const isOpen = accountMenu.classList.toggle("is-open");
        accountMenuButton.setAttribute("aria-expanded", String(isOpen));
    });

    document.addEventListener("click", (event) => {
        if (!accountMenu?.contains(event.target)) {
            accountMenu?.classList.remove("is-open");
            accountMenuButton?.setAttribute("aria-expanded", "false");
        }
    });

    document.querySelector("#logout-button")?.addEventListener("click", async (event) => {
        const logoutButton = event.currentTarget;

        logoutButton.disabled = true;
        logoutButton.textContent = "Logging out...";

        try {
            let response = await fetch(document.body.dataset.customerLogoutUrl, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                credentials: "same-origin",
            });

            if (response.status === 419) {
                await refreshCsrfToken();
                response = await fetch(document.body.dataset.customerLogoutUrl, {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    credentials: "same-origin",
                });
            }

            if (!response.ok) {
                throw new Error("Unable to log out. Please refresh and try again.");
            }

            sessionStorage.removeItem("eluzeAuthModalClosed");
            window.location.reload();
        } catch (error) {
            logoutButton.disabled = false;
            logoutButton.textContent = "Logout";
            window.alert(error.message);
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            accountMenu?.classList.remove("is-open");
            accountMenuButton?.setAttribute("aria-expanded", "false");
        }

        if (event.key === "Escape" && !authModal.hidden) {
            closeAuthModal();
        }
    });

    const shouldForceLogin = new URLSearchParams(window.location.search).has("login");

    if (document.body.dataset.authenticated !== "true" && (shouldForceLogin || sessionStorage.getItem("eluzeAuthModalClosed") !== "true")) {
        if (shouldForceLogin) {
            sessionStorage.removeItem("eluzeAuthModalClosed");
        }
        window.setTimeout(openAuthModal, 600);
    }
}

const modal = document.querySelector("#order-modal");

if (modal) {
    const productName = modal.querySelector("#order-product-name");
    const productScent = modal.querySelector("#order-product-scent");
    const productInspired = modal.querySelector("#order-product-inspired");
    const quantityOutput = modal.querySelector("#order-quantity");
    const priceOutput = modal.querySelector("#order-price");
    const feedback = modal.querySelector("#order-feedback");
    const cartLink = document.querySelector(".cart-link");
    const inspirationByProduct = {
        DAYDREAM: "morning light and soft clouds",
        LIORA: "a garden in full bloom",
        SOLENNE: "quiet evenings and silk",
    };
    let quantity = 1;
    let unitPrice = 850;
    let selectedSize = 10;
    let lastTrigger = null;
    let currentProductId = null;
    let currentStock = 0;
    const readCart = () => {
        try {
            return JSON.parse(localStorage.getItem("eluzeCart")) || [];
        } catch {
            return [];
        }
    };

    const updateCartBadge = () => {
        const count = readCart().reduce((total, item) => total + item.quantity, 0);

        if (count > 0) {
            cartLink.dataset.count = count;
        } else {
            delete cartLink.dataset.count;
        }
    };

    const currency = new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
        maximumFractionDigits: 0,
    });

    const updatePrice = () => {
        priceOutput.textContent = currency.format(unitPrice * quantity);
    };

    const priceFromTrigger = (trigger, size) => {
        const price = Number(trigger.dataset[`price${size}`]);

        return Number.isFinite(price) && price > 0 ? price : Number(modal.querySelector(`[data-size="${size}"]`)?.dataset.price || 850);
    };

    const closeModal = () => {
        modal.hidden = true;
        document.body.classList.remove("modal-open");
        feedback.textContent = "";
        lastTrigger?.focus();
    };

    const openModal = (trigger) => {
        const card = trigger.closest(".product-card");
        const name = trigger.dataset.name || card?.querySelector("h3")?.textContent.trim() || "ELUZE";
        const scent = trigger.dataset.scent || card?.querySelector("p")?.textContent.trim() || "signature fragrance";
        const stock = Number(trigger.dataset.stock || card?.dataset.stock || 0);
        const collection = card?.closest(".collection-section");
        const category = collection?.classList.contains("men-collection")
            ? "Men's Collection"
            : collection?.classList.contains("unisex-collection")
                ? "Unisex Collection"
                : "Women's Collection";

        if (stock <= 0) {
            return;
        }

        lastTrigger = trigger;
        currentProductId = Number(trigger.dataset.productId || card?.dataset.productId || 0) || null;
        currentStock = stock;
        modal.dataset.category = category;
        quantity = 1;
        selectedSize = 10;
        unitPrice = priceFromTrigger(trigger, selectedSize);
        productName.textContent = name;
        productScent.textContent = scent;
        productInspired.textContent = trigger.dataset.inspiration || card?.dataset.inspiration || inspirationByProduct[name] || `the character of ${name.toLowerCase()}`;
        quantityOutput.textContent = quantity;
        feedback.textContent = "";
        modal.querySelectorAll("[data-size]").forEach((button) => {
            button.dataset.price = priceFromTrigger(trigger, button.dataset.size);
        lastTrigger = trigger;
        modal.dataset.category = category;
        quantity = 1;
        unitPrice = 850;
        selectedSize = 10;
        productName.textContent = name;
        productScent.textContent = scent;
        productInspired.textContent = inspirationByProduct[name] || `the character of ${name.toLowerCase()}`;
        quantityOutput.textContent = quantity;
        feedback.textContent = "";
        modal.querySelectorAll("[data-size]").forEach((button) => {
            button.classList.toggle("is-selected", button.dataset.size === "10");
        });
        updatePrice();
        modal.hidden = false;
        document.body.classList.add("modal-open");
        modal.querySelector(".order-dialog__close").focus();
    };

    document.querySelectorAll(".product-card").forEach((card) => {
        if (Number(card.dataset.stock || 0) <= 0) {
            card.setAttribute("aria-disabled", "true");
            return;
        }

        card.classList.add("perfume-trigger");
        card.tabIndex = 0;
        card.setAttribute("role", "button");
        card.setAttribute("aria-label", `Order ${card.querySelector("h3").textContent.trim()} perfume`);
    });

    document.querySelectorAll(".perfume-trigger").forEach((trigger) => {
        trigger.addEventListener("click", () => openModal(trigger));
        trigger.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openModal(trigger);
            }
        });
    });

    modal.querySelectorAll("[data-close-modal]").forEach((button) => {
        button.addEventListener("click", closeModal);
    });

    modal.querySelectorAll("[data-size]").forEach((button) => {
        button.addEventListener("click", () => {
            selectedSize = Number(button.dataset.size);
            unitPrice = Number(button.dataset.price);
            modal.querySelectorAll("[data-size]").forEach((option) => option.classList.remove("is-selected"));
            button.classList.add("is-selected");
            updatePrice();
        });
    });

    modal.querySelectorAll("[data-quantity-action]").forEach((button) => {
        button.addEventListener("click", () => {
            quantity = button.dataset.quantityAction === "increase"
                ? Math.min(quantity + 1, currentStock, 99)
                ? Math.min(quantity + 1, 99)
                : Math.max(quantity - 1, 1);
            quantityOutput.textContent = quantity;
            updatePrice();
        });
    });

    const addCurrentItemToCart = () => {
    modal.querySelector("#add-to-cart").addEventListener("click", () => {
        const cart = readCart();
        const itemId = `${productName.textContent}-${selectedSize}`.toLowerCase().replace(/\s+/g, "-");
        const existingItem = cart.find((item) => item.id === itemId);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: itemId,
                productId: currentProductId,
                name: productName.textContent,
                scent: productScent.textContent,
                category: modal.dataset.category,
                size: selectedSize,
                unitPrice,
                quantity,
            });
        }

        localStorage.setItem("eluzeCart", JSON.stringify(cart));
        updateCartBadge();
        feedback.textContent = `${quantity} x ${productName.textContent} (${selectedSize} ML) added to cart.`;
        return itemId;
    };

    modal.querySelector("#add-to-cart").addEventListener("click", () => {
        addCurrentItemToCart();
    });

    modal.querySelector("#buy-now").addEventListener("click", () => {
        const itemId = addCurrentItemToCart();
        sessionStorage.setItem("eluzeCheckoutNow", itemId);
        window.location.href = `${cartLink.href}?checkout=details`;
    });

    modal.querySelector("#buy-now").addEventListener("click", () => {
        modal.querySelector("#add-to-cart").click();
        window.location.href = cartLink.href;
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !modal.hidden) {
            closeModal();
        }
    });

    updateCartBadge();
}
