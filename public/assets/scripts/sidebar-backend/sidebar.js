// Tabel submenu toggle with animation
const tabelToggle = document.getElementById("tabel-toggle");
const tabelSubmenu = document.getElementById("tabel-submenu");
const tabelIcon = document.getElementById("tabel-icon");

if (tabelToggle && tabelSubmenu && tabelIcon) {
    let isSubmenuOpen = false;

    tabelToggle.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        isSubmenuOpen = !isSubmenuOpen;

        if (isSubmenuOpen) {
            // Open submenu with smooth animation
            tabelSubmenu.style.maxHeight = tabelSubmenu.scrollHeight + "px";
            tabelIcon.style.transform = "rotate(90deg)";
            tabelToggle.classList.add("bg-gray-50");
        } else {
            // Close submenu with smooth animation
            tabelSubmenu.style.maxHeight = "0";
            tabelIcon.style.transform = "rotate(0deg)";
            tabelToggle.classList.remove("bg-gray-50");
        }
    });
}

// Menu navigation - exclude parent menu
const menuItems = document.querySelectorAll(".menu-item, .submenu-item");
const breadcrumb = document.getElementById("breadcrumb");
