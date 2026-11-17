
document.addEventListener("DOMContentLoaded", function () {

    const navbar = document.querySelector(".navbar");
    const menuButton = document.querySelector(".menu-toggle");
    const navigation = document.querySelector("#main-navigation");

    // Stop if the required elements do not exist
    if (!navbar || !menuButton || !navigation) {
        return;
    }

    // Open and close the mobile menu
    menuButton.addEventListener("click", function () {

        const isOpen = navbar.classList.toggle("menu-open");

        menuButton.setAttribute("aria-expanded", String(isOpen));

        menuButton.setAttribute(
            "aria-label",
            isOpen ? "Close navigation menu" : "Open navigation menu"
        );

    });

    // Close the menu after clicking a navigation link
    navigation.querySelectorAll("a").forEach(function (link) {

        link.addEventListener("click", function () {

            navbar.classList.remove("menu-open");

            menuButton.setAttribute("aria-expanded", "false");

            menuButton.setAttribute(
                "aria-label",
                "Open navigation menu"
            );

        });

    });

    // Close the menu when Escape is pressed
    document.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {

            navbar.classList.remove("menu-open");

            menuButton.setAttribute("aria-expanded", "false");

            menuButton.setAttribute(
                "aria-label",
                "Open navigation menu"
            );

        }

    });

    // Close the mobile menu when returning to desktop width
    window.addEventListener("resize", function () {

        if (window.innerWidth > 850) {

            navbar.classList.remove("menu-open");

            menuButton.setAttribute("aria-expanded", "false");

        }

    });

});
