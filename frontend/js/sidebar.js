const toggleButton = document.getElementById('toggle-btn');
const sidebar = document.getElementById('sidebar');

function toggleSidebar() {
    sidebar.classList.toggle('close');
}

function toggleSubMenu(button) {
    document.querySelectorAll('.sub-menu.show').forEach(menu => {
        menu.classList.remove('show');
        menu.previousElementSibling.classList.remove('rotate');
    });
    
    button.nextElementSibling.classList.toggle('show');
}

toggleButton.addEventListener('click', () => {
    sidebar.classList.toggle('open');
});
