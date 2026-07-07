const trackingPage = document.querySelector(".order-track-page");

if (trackingPage) {
    const statusLabel = document.querySelector("[data-status-label]");
    const statusHelp = document.querySelector("[data-status-help]");
    const statusPill = document.querySelector("[data-status-pill]");
    const trackingNumber = document.querySelector("[data-tracking-number]");
    const updatedAt = document.querySelector("[data-updated-at]");
    const trackingSteps = document.querySelector(".tracking-steps");
    const statusHelpText = {
        pending: "Waiting for confirmation",
        processing: "Preparing your order",
        delivered: "Order completed",
    };
    const statusOrder = ["pending", "processing", "delivered"];

    const updateTimeline = (status) => {
        const activeIndex = statusOrder.indexOf(status);

        trackingSteps.dataset.currentStatus = status;
        trackingSteps.querySelectorAll("[data-step]").forEach((step) => {
            const stepIndex = statusOrder.indexOf(step.dataset.step);
            step.classList.toggle("is-complete", stepIndex < activeIndex);
            step.classList.toggle("is-active", stepIndex === activeIndex);
        });
    };

    const updateTracking = (data) => {
        statusLabel.textContent = data.statusLabel;
        statusHelp.textContent = statusHelpText[data.status] || "Status updated";
        statusPill.textContent = data.statusLabel;
        statusPill.className = `status-pill status-pill--${data.status}`;
        trackingNumber.textContent = data.trackingNumber;
        updatedAt.textContent = data.updatedAt;
        updateTimeline(data.status);
    };

    const fetchTracking = async () => {
        try {
            const response = await fetch(trackingPage.dataset.trackingUrl, {
                headers: {
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                return;
            }

            updateTracking(await response.json());
        } catch {
            // Keep the last known status visible if the network is unavailable.
        }
    };

    updateTimeline(trackingSteps.dataset.currentStatus);
    window.setInterval(fetchTracking, 15000);
}
