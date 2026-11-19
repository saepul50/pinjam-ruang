document.querySelectorAll("[data-toast]").forEach((toast) => {
    window.setTimeout(() => toast.classList.remove("show"), 3000);
});

document.querySelectorAll("[data-confirm-delete]").forEach((form) => {
    form.addEventListener("submit", (event) => {
        if (!window.confirm("Yakin menghapus data ini?")) {
            event.preventDefault();
        }
    });
});

const menuToggle = document.querySelector(".menu-toggle");
const nav = document.querySelector(".nav");

if (menuToggle && nav) {
    menuToggle.addEventListener("click", () => {
        const open = nav.classList.toggle("open");
        menuToggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
}

document.querySelectorAll("[data-modal-open]").forEach((trigger) => {
    trigger.addEventListener("click", () => {
        const modal = document.querySelector(
            trigger.dataset.modalOpen,
        );
        if (!modal) return;
        ["name", "capacity", "location"].forEach((field) => {
            const target = modal.querySelector("[data-modal-" + field + "]");
            if (target) target.textContent = trigger.dataset[field] || "";
        });
        modal.classList.add("show");
    });
});

document.querySelectorAll("[data-modal-close]").forEach((trigger) => {
    trigger.addEventListener("click", () => {
        const modal = trigger.closest(".modal-overlay");
        if (modal) modal.classList.remove("show");
    });
});
