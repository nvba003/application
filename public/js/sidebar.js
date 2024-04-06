document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.querySelector('.toggle-sidebar');
    const sidebar = document.getElementById('sidebar');

    toggleButton.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.menu-toggle');

    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const subMenu = this.nextElementSibling; // Lấy phần tử ngay sau nút toggle, tức là danh sách con
            if (subMenu.style.display === "none") {
                subMenu.style.display = "block"; // Hiển thị danh sách con
            } else {
                subMenu.style.display = "none"; // Ẩn danh sách con
            }
        });
    });
});

