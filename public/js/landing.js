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
        const collection = card?.closest(".collection-section");
        const category = collection?.classList.contains("men-collection")
            ? "Men's Collection"
            : collection?.classList.contains("unisex-collection")
                ? "Unisex Collection"
                : "Women's Collection";

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
                ? Math.min(quantity + 1, 99)
                : Math.max(quantity - 1, 1);
            quantityOutput.textContent = quantity;
            updatePrice();
        });
    });

    modal.querySelector("#add-to-cart").addEventListener("click", () => {
        const cart = readCart();
        const itemId = `${productName.textContent}-${selectedSize}`.toLowerCase().replace(/\s+/g, "-");
        const existingItem = cart.find((item) => item.id === itemId);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: itemId,
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
