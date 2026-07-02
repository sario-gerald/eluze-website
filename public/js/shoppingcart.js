const cartList = document.querySelector("#cart-list");
const emptyCart = document.querySelector("#empty-cart");
const checkoutBar = document.querySelector("#checkout-bar");
const template = document.querySelector("#cart-item-template");
const selectAll = document.querySelector("#select-all");
const totalOutput = document.querySelector("#cart-total");
const editButton = document.querySelector("#edit-cart");
const checkoutButton = document.querySelector("#checkout-button");
const feedback = document.querySelector("#checkout-feedback");

const currency = new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
    maximumFractionDigits: 0,
});

let cart = [];
let selectedIds = new Set();

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

const updateSummary = () => {
    const selectedItems = cart.filter((item) => selectedIds.has(item.id));
    const total = selectedItems.reduce((sum, item) => sum + item.unitPrice * item.quantity, 0);

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
        fragment.querySelector(".item-price").textContent = currency.format(item.unitPrice);
        fragment.querySelector("output").textContent = item.quantity;
        checkbox.checked = selectedIds.has(item.id);
        cartList.append(fragment);
    });

    updateSummary();
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
    const count = cart
        .filter((item) => selectedIds.has(item.id))
        .reduce((sum, item) => sum + item.quantity, 0);
    feedback.textContent = `${count} item${count === 1 ? "" : "s"} selected for checkout.`;
});

cart = readCart();
selectedIds = new Set(cart.map((item) => item.id));
renderCart();
