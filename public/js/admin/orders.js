const modal = document.querySelector('[data-tracking-modal]');
const form = document.querySelector('[data-tracking-form]');
const statusInput = document.querySelector('[data-modal-status]');
const urlInput = document.querySelector('[data-modal-url]');
const trackingInput = document.querySelector('[data-modal-tracking]');
const errorMessage = document.querySelector('[data-modal-error]');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

let activeActionGroup = null;

const statusesRequiringTracking = ['processing', 'delivered'];

function showError(message) {
    errorMessage.textContent = message;
    errorMessage.hidden = false;
}

function clearError() {
    errorMessage.textContent = '';
    errorMessage.hidden = true;
}

function openTrackingModal(actionGroup, status, trackingNumber) {
    activeActionGroup = actionGroup;
    statusInput.value = status;
    urlInput.value = actionGroup.dataset.updateUrl;
    trackingInput.value = trackingNumber || '';
    clearError();
    modal.hidden = false;
    trackingInput.focus();
}

function closeTrackingModal() {
    modal.hidden = true;
    activeActionGroup = null;
    form.reset();
    clearError();
}

function updateRow(order) {
    const row = document.querySelector(`[data-order-row="${order.id}"]`);

    if (!row) {
        return;
    }

    const statusPill = row.querySelector('[data-status-pill]');
    const trackingCell = row.querySelector('[data-tracking-cell]');
    const actionButtons = row.querySelectorAll('[data-status-action]');

    statusPill.textContent = order.status.charAt(0).toUpperCase() + order.status.slice(1);
    statusPill.className = `status-pill status-pill--${order.status}`;
    trackingCell.textContent = order.tracking_number || 'Awaiting tracking';

    actionButtons.forEach((button) => {
        button.dataset.currentStatus = order.status;
        button.dataset.currentTracking = order.tracking_number || '';
        button.disabled = button.dataset.status === order.status;
    });
}

async function submitStatusUpdate(url, status, trackingNumber = null) {
    const response = await fetch(url, {
        method: 'PATCH',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            status,
            tracking_number: trackingNumber,
        }),
    });

    const data = await response.json();

    if (!response.ok) {
        const fallback = 'Unable to update this order. Please review the tracking number and try again.';
        throw new Error(data.message || fallback);
    }

    return data.order;
}

document.addEventListener('click', async (event) => {
    const actionButton = event.target.closest('[data-status-action]');

    if (!actionButton) {
        return;
    }

    const actionGroup = actionButton.closest('.action-group');
    const status = actionButton.dataset.status;
    const currentTracking = actionButton.dataset.currentTracking;

    if (statusesRequiringTracking.includes(status)) {
        openTrackingModal(actionGroup, status, currentTracking);
        return;
    }

    actionButton.disabled = true;

    try {
        const order = await submitStatusUpdate(actionGroup.dataset.updateUrl, status);
        updateRow(order);
    } catch (error) {
        actionButton.disabled = false;
        window.alert(error.message);
    }
});

form?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const submitButton = form.querySelector('button[type="submit"]');
    const trackingNumber = trackingInput.value.trim();

    if (!trackingNumber) {
        showError('Enter a tracking number before updating this order.');
        return;
    }

    submitButton.disabled = true;
    clearError();

    try {
        const order = await submitStatusUpdate(urlInput.value, statusInput.value, trackingNumber);
        updateRow(order);
        closeTrackingModal();
    } catch (error) {
        showError(error.message);
    } finally {
        submitButton.disabled = false;
    }
});

document.querySelector('[data-modal-close]')?.addEventListener('click', closeTrackingModal);
document.querySelector('[data-modal-cancel]')?.addEventListener('click', closeTrackingModal);

modal?.addEventListener('click', (event) => {
    if (event.target === modal) {
        closeTrackingModal();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal && !modal.hidden) {
        closeTrackingModal();
    }
});
