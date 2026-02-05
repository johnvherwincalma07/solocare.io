// Navbar Login Button -> Modal
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("loginModal");
    const openBtn = document.getElementById("openLoginModal");
    const closeBtn = document.querySelector(".close-modal");

    if (openBtn) {
        openBtn.addEventListener("click", () => {
            modal.style.display = "flex";
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
