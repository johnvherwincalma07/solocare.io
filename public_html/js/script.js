// Example: Highlight active nav link on scroll
document.addEventListener("scroll", function () {
    let sections = document.querySelectorAll("section");
    let scrollPos = document.documentElement.scrollTop || document.body.scrollTop;

    sections.forEach(sec => {
        if (scrollPos >= sec.offsetTop - 80 && scrollPos < sec.offsetTop + sec.offsetHeight) {
            document.querySelectorAll(".nav-link").forEach(link => {
                link.classList.remove("active");
                if (link.getAttribute("href") === "#" + sec.id) {
                    link.classList.add("active");
                }
            });
        }
    });
});
