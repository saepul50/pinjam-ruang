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
const sidebarContent = document.querySelector(".sidebar-content");
const navBackdrop = document.querySelector(".nav-backdrop");

if (menuToggle && sidebarContent && navBackdrop) {
    const setMenuOpen = (open) => {
        sidebarContent.classList.toggle("open", open);
        navBackdrop.classList.toggle("show", open);
        menuToggle.setAttribute("aria-expanded", open ? "true" : "false");
        menuToggle.setAttribute(
            "aria-label",
            open ? "Tutup navigasi" : "Buka navigasi",
        );
    };

    menuToggle.addEventListener("click", () => {
        setMenuOpen(!sidebarContent.classList.contains("open"));
    });
    navBackdrop.addEventListener("click", () => setMenuOpen(false));
    sidebarContent.querySelectorAll(".nav a").forEach((link) => {
        link.addEventListener("click", () => setMenuOpen(false));
    });
    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") setMenuOpen(false);
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
