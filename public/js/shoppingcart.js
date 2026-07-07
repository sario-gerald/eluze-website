const cartList = document.querySelector("#cart-list");
const emptyCart = document.querySelector("#empty-cart");
const checkoutBar = document.querySelector("#checkout-bar");
const template = document.querySelector("#cart-item-template");
const selectAll = document.querySelector("#select-all");
const totalOutput = document.querySelector("#cart-total");
const editButton = document.querySelector("#edit-cart");
const checkoutButton = document.querySelector("#checkout-button");
const feedback = document.querySelector("#checkout-feedback");
const cartToolbar = document.querySelector("#cart-toolbar");
const detailsForm = document.querySelector("#details-form");
const orderCheckout = document.querySelector("#order-checkout");
const thankYouView = document.querySelector("#thank-you-view");
const checkoutItems = document.querySelector("#checkout-items");
const checkoutItemTemplate = document.querySelector("#checkout-item-template");
const summarySubtotal = document.querySelector("#summary-subtotal");
const summaryShipping = document.querySelector("#summary-shipping");
const summaryTotal = document.querySelector("#summary-total");
const deliveryNote = document.querySelector("#delivery-note");
const termsCheck = document.querySelector("#terms-check");
const placeOrder = document.querySelector("#place-order");
const orderFeedback = document.querySelector("#order-feedback");
const cartPage = document.querySelector(".cart-page");
const orderUrl = cartPage.dataset.orderUrl;
const isCustomerLoggedIn = cartPage.dataset.authenticated === "true";
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
const regionSelect = detailsForm.elements.region;
const citySelect = detailsForm.elements.city;
const barangaySelect = detailsForm.elements.barangay;
const currentCustomerProfile = (() => {
    const profileScript = document.querySelector("#current-customer-profile");

    if (!profileScript) {
        return null;
    }

    try {
        return JSON.parse(profileScript.textContent);
    } catch {
        return null;
    }
})();

const currency = new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
    maximumFractionDigits: 0,
});

let cart = [];
let selectedIds = new Set();
let checkoutIds = new Set();
const shippingFee = 32;

const compactCurrency = new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
    maximumFractionDigits: 0,
});

const dateFormatter = new Intl.DateTimeFormat("en-US", {
    month: "long",
    day: "numeric",
    timeZone: "Asia/Manila",
});

const addDays = (date, days) => {
    const nextDate = new Date(date);
    nextDate.setDate(nextDate.getDate() + days);
    return nextDate;
};

const formatDeliveryWindow = () => {
    const today = new Date();
    const startDate = addDays(today, 3);
    const endDate = addDays(today, 4);
    const startMonth = new Intl.DateTimeFormat("en-US", {
        month: "long",
        timeZone: "Asia/Manila",
    }).format(startDate);
    const endMonth = new Intl.DateTimeFormat("en-US", {
        month: "long",
        timeZone: "Asia/Manila",
    }).format(endDate);
    const startDay = new Intl.DateTimeFormat("en-US", {
        day: "numeric",
        timeZone: "Asia/Manila",
    }).format(startDate);
    const endDay = new Intl.DateTimeFormat("en-US", {
        day: "numeric",
        timeZone: "Asia/Manila",
    }).format(endDate);

    if (startMonth === endMonth) {
        return `Get by ${startMonth} ${startDay}-${endDay}`;
    }

    return `Get by ${dateFormatter.format(startDate)} - ${dateFormatter.format(endDate)}`;
};

const readCart = () => {
    try {
        return JSON.parse(localStorage.getItem("eluzeCart")) || [];
    } catch {
        return [];
    }
};

const saveCart = () => {
    localStorage.setItem("eluzeCart", JSON.stringify(cart));
};

const readDetails = () => {
    try {
        const savedDetails = JSON.parse(localStorage.getItem("eluzeCheckoutDetails")) || null;

        if (currentCustomerProfile?.email && savedDetails?.email !== currentCustomerProfile.email) {
            return currentCustomerProfile;
        }

        return savedDetails || currentCustomerProfile || null;
    } catch {
        return currentCustomerProfile || null;
    }
};

const saveDetails = (details) => {
    localStorage.setItem("eluzeCheckoutDetails", JSON.stringify(details));
};

const getCheckoutItems = () => cart.filter((item) => checkoutIds.has(item.id));

const psgcApi = "https://psgc.gitlab.io/api";

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
        throw new Error("Address list is unavailable right now. Please try again.");
    }

    return response.json();
};

const loadRegions = async (selectedRegion = "") => {
    setSelectMessage(regionSelect, "Loading regions...");
    citySelect.disabled = true;
    barangaySelect.disabled = true;
    setSelectMessage(citySelect, "Choose region first");
    setSelectMessage(barangaySelect, "Choose city first");

    try {
        const regions = await fetchPsgc("/regions/");
        populateSelect(regionSelect, regions, "Region");
        regionSelect.value = selectedRegion;
        regionSelect.disabled = false;
    } catch (error) {
        setSelectMessage(regionSelect, error.message);
    }
};

const loadCities = async (selectedCity = "") => {
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
        citySelect.value = selectedCity;
        citySelect.disabled = false;
    } catch (error) {
        setSelectMessage(citySelect, error.message);
    }
};

const loadBarangays = async (selectedBarangay = "") => {
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
        barangaySelect.value = selectedBarangay;
        barangaySelect.disabled = false;
    } catch (error) {
        setSelectMessage(barangaySelect, error.message);
    }
};

const restoreAddressSelects = async () => {
    const details = readDetails();

    await loadRegions(details?.region || "");
    await loadCities(details?.city || "");
    await loadBarangays(details?.barangay || "");
};

const showView = (view) => {
    const showingCart = view === "cart";
    const showingDetails = view === "details";
    const showingCheckout = view === "checkout";
    const showingThankYou = view === "thank-you";

    cartToolbar.hidden = !showingCart;
    cartList.hidden = !showingCart;
    emptyCart.hidden = !showingCart || cart.length > 0;
    checkoutBar.hidden = !showingCart || cart.length === 0;
    detailsForm.hidden = !showingDetails;
    orderCheckout.hidden = !showingCheckout;
    thankYouView.hidden = !showingThankYou;
    document.body.classList.toggle("is-checkout-view", showingDetails || showingCheckout);
    document.body.classList.toggle("is-thank-you-view", showingThankYou);
};

const updateSummary = () => {
    const selectedItems = cart.filter((item) => selectedIds.has(item.id));
    const total = selectedItems.reduce((sum, item) => sum + item.unitPrice * item.quantity, 0);

    totalOutput.textContent = compactCurrency.format(total);
    totalOutput.textContent = currency.format(total);
    checkoutButton.disabled = selectedItems.length === 0;
    selectAll.checked = cart.length > 0 && selectedItems.length === cart.length;
    selectAll.indeterminate = selectedItems.length > 0 && selectedItems.length < cart.length;
};

const renderCart = () => {
    cartList.replaceChildren();
    emptyCart.hidden = cart.length > 0;
    checkoutBar.hidden = cart.length === 0;

    cart.forEach((item) => {
        const fragment = template.content.cloneNode(true);
        const card = fragment.querySelector(".cart-item");
        const checkbox = fragment.querySelector(".item-checkbox");

        card.dataset.id = item.id;
        fragment.querySelector("h2").textContent = `ELUZE Perfume 24hrs Long Lasting | ${item.category}`;
        fragment.querySelector(".item-variant").textContent = `${item.name} ${item.size}ml`;
        fragment.querySelector(".item-price").textContent = compactCurrency.format(item.unitPrice);
        fragment.querySelector(".item-price").textContent = currency.format(item.unitPrice);
        fragment.querySelector("output").textContent = item.quantity;
        checkbox.checked = selectedIds.has(item.id);
        cartList.append(fragment);
    });

    updateSummary();
};

const fillDetailsForm = async (options = {}) => {
    const shouldRestoreAddress = options.restoreAddress !== false;
    const details = readDetails();

    if (!details) {
        return;
    }

    if (shouldRestoreAddress) {
        await restoreAddressSelects();
    }

    Object.entries(details).forEach(([key, value]) => {
        const field = detailsForm.elements[key];

        if (field) {
            field.value = value;
        }
    });
};

const renderCheckout = () => {
    const items = getCheckoutItems();
    const subtotal = items.reduce((sum, item) => sum + item.unitPrice * item.quantity, 0);
    const total = subtotal + (items.length > 0 ? shippingFee : 0);

    checkoutItems.replaceChildren();
    items.forEach((item) => {
        const fragment = checkoutItemTemplate.content.cloneNode(true);
        const card = fragment.querySelector(".checkout-item");

        card.dataset.id = item.id;
        fragment.querySelector("h2").textContent = `ELUZE Perfume 24hrs Long Lasting | ${item.category}`;
        fragment.querySelector("p").textContent = `${item.name} ${item.size}ml`;
        fragment.querySelector("strong").textContent = compactCurrency.format(item.unitPrice);
        fragment.querySelector("output").textContent = item.quantity;
        checkoutItems.append(fragment);
    });

    summarySubtotal.textContent = currency.format(subtotal);
    summaryShipping.textContent = currency.format(items.length > 0 ? shippingFee : 0);
    summaryTotal.textContent = currency.format(total);
    deliveryNote.textContent = formatDeliveryWindow();
};

const startCheckout = () => {
    if (!isCustomerLoggedIn) {
        feedback.textContent = "Please log in before checking out.";
        window.setTimeout(() => {
            window.location.href = "/?login=checkout";
        }, 650);
        return;
    }

    const buyNowId = sessionStorage.getItem("eluzeCheckoutNow");

    checkoutIds = new Set(
        buyNowId && cart.some((item) => item.id === buyNowId)
            ? [buyNowId]
            : selectedIds.size > 0
            ? [...selectedIds]
            : cart.map((item) => item.id)
    );
    sessionStorage.removeItem("eluzeCheckoutNow");

    if (checkoutIds.size === 0) {
        feedback.textContent = "Select an item before checkout.";
        return;
    }

    fillDetailsForm();
    showView("details");
};

cartList.addEventListener("click", (event) => {
    const card = event.target.closest(".cart-item");

    if (!card) {
        return;
    }

    const item = cart.find((entry) => entry.id === card.dataset.id);

    if (event.target.matches("[data-action]")) {
        item.quantity = event.target.dataset.action === "increase"
            ? Math.min(item.quantity + 1, 99)
            : Math.max(item.quantity - 1, 1);
        saveCart();
        renderCart();
    }

    if (event.target.matches(".remove-item")) {
        cart = cart.filter((entry) => entry.id !== item.id);
        selectedIds.delete(item.id);
        saveCart();
        renderCart();
    }
});

cartList.addEventListener("change", (event) => {
    if (!event.target.matches(".item-checkbox")) {
        return;
    }

    const id = event.target.closest(".cart-item").dataset.id;
    event.target.checked ? selectedIds.add(id) : selectedIds.delete(id);
    updateSummary();
});

selectAll.addEventListener("change", () => {
    selectedIds = selectAll.checked ? new Set(cart.map((item) => item.id)) : new Set();
    renderCart();
});

editButton.addEventListener("click", () => {
    const editing = cartList.classList.toggle("is-editing");
    editButton.textContent = editing ? "Done" : "Edit";
});

checkoutButton.addEventListener("click", () => {
    startCheckout();
});

detailsForm.addEventListener("submit", (event) => {
    event.preventDefault();

    if (!detailsForm.reportValidity()) {
        return;
    }

    const data = new FormData(detailsForm);
    saveDetails(Object.fromEntries(data.entries()));
    renderCheckout();
    showView("checkout");
});

document.querySelectorAll("[data-view-cart]").forEach((button) => {
    button.addEventListener("click", () => showView("cart"));
});

document.querySelectorAll("[data-edit-details]").forEach((button) => {
    button.addEventListener("click", async () => {
        await fillDetailsForm();
        showView("details");
    });
});

regionSelect.addEventListener("change", () => {
    loadCities();
});

citySelect.addEventListener("change", () => {
    loadBarangays();
});

checkoutItems.addEventListener("click", (event) => {
    if (!event.target.matches("[data-checkout-action]")) {
        return;
    }

    const item = cart.find((entry) => entry.id === event.target.closest(".checkout-item").dataset.id);
    item.quantity = event.target.dataset.checkoutAction === "increase"
        ? Math.min(item.quantity + 1, 99)
        : Math.max(item.quantity - 1, 1);
    saveCart();
    renderCart();
    renderCheckout();
});

placeOrder.addEventListener("click", async () => {
    if (!isCustomerLoggedIn) {
        orderFeedback.textContent = "Please log in before placing your order.";
        window.setTimeout(() => {
            window.location.href = "/?login=checkout";
        }, 650);
        return;
    }

    if (!termsCheck.checked) {
        termsCheck.focus();
        orderFeedback.textContent = "Please agree to the terms before placing your order.";
        return;
    }

    const details = readDetails();
    const items = getCheckoutItems().map((item) => ({
        productId: item.productId || null,
        name: item.name,
        category: item.category,
        scent: item.scent,
        size: item.size,
        unitPrice: item.unitPrice,
        quantity: item.quantity,
    }));

    if (!details) {
        fillDetailsForm();
        showView("details");
        return;
    }

    if (items.length === 0) {
        orderFeedback.textContent = "Select at least one item before placing your order.";
        showView("cart");
        return;
    }

    placeOrder.disabled = true;
    orderFeedback.textContent = "Placing your order...";

    try {
        const response = await fetch(orderUrl, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                ...details,
                items,
            }),
        });
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || "Unable to place your order. Please check your details.");
        }

        feedback.textContent = `Your order ${result.order_reference || `#${String(result.order_id).padStart(5, "0")}`} has been placed.`;
    } catch (error) {
        orderFeedback.textContent = error.message;
        placeOrder.disabled = false;
        return;
    }

    const orderedIds = new Set(checkoutIds);
    cart = cart.filter((item) => !orderedIds.has(item.id));
    selectedIds = new Set(cart.map((item) => item.id));
    checkoutIds = new Set();
    saveCart();
    renderCart();
    termsCheck.checked = false;
    orderFeedback.textContent = "";
    placeOrder.disabled = false;
    showView("thank-you");
    window.scrollTo({ top: 0, behavior: "smooth" });
    const count = cart
        .filter((item) => selectedIds.has(item.id))
        .reduce((sum, item) => sum + item.quantity, 0);
    feedback.textContent = `${count} item${count === 1 ? "" : "s"} selected for checkout.`;
});

cart = readCart();
selectedIds = new Set(cart.map((item) => item.id));
renderCart();

if (readDetails()) {
    fillDetailsForm();
} else {
    restoreAddressSelects();
}

if (new URLSearchParams(window.location.search).get("checkout") === "details" && cart.length > 0) {
    selectedIds = new Set(cart.map((item) => item.id));
    renderCart();
    startCheckout();
} else {
    showView("cart");
}
