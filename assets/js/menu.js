// Función para abrir/cerrar el sidebar
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
    if (overlay) {
        overlay.classList.toggle('show');
    }
}

// Cerrar el menú al hacer clic fuera (en móvil)
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    var toggle = document.querySelector('.menu-toggle');
    
    if (sidebar && toggle) {
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                    if (overlay) {
                        overlay.classList.remove('show');
                    }
                }
            }
        });
    }
});