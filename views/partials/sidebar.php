<nav class="sidebar" id="sidebar">
    <div class="brand">
        <i class="fas fa-tools"></i>
        <h3>TallerTech</h3>
        <span>Electrónica</span>
    </div>

    <div class="menu">
        <div class="menu-label">Navegación</div>
        
        <a href="index.php?controller=dashboard&action=index" 
           class="<?= (isset($_GET['controller']) && $_GET['controller'] == 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        
        <a href="index.php?controller=cliente&action=index" 
           class="<?= (isset($_GET['controller']) && $_GET['controller'] == 'cliente') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Clientes
        </a>
        
        <a href="index.php?controller=tecnico&action=index" 
           class="<?= (isset($_GET['controller']) && $_GET['controller'] == 'tecnico') ? 'active' : '' ?>">
            <i class="fas fa-user-cog"></i> Técnicos
        </a>
        
        <a href="index.php?controller=equipo&action=index" 
           class="<?= (isset($_GET['controller']) && $_GET['controller'] == 'equipo') ? 'active' : '' ?>">
            <i class="fas fa-desktop"></i> Equipos
        </a>
        
        <a href="index.php?controller=repuesto&action=index" 
           class="<?= (isset($_GET['controller']) && $_GET['controller'] == 'repuesto') ? 'active' : '' ?>">
            <i class="fas fa-microchip"></i> Repuestos
        </a>
        
        <a href="index.php?controller=orden&action=index" 
           class="<?= (isset($_GET['controller']) && $_GET['controller'] == 'orden') ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i> Órdenes
        </a>
        
        <a href="index.php?controller=orden&action=reporteTecnico" 
           class="<?= (isset($_GET['action']) && $_GET['action'] == 'reporteTecnico') ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i> Reportes
        </a>
    </div>

    <a href="index.php?controller=login&action=logout" class="logout">
        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
    </a>
</nav>

<!-- Overlay para móvil -->
<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>