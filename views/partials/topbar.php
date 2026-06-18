<div class="top-bar">
    <div>
        <button class="menu-toggle" onclick="toggleSidebar()" style="background: none; border: none; font-size: 24px; color: #1e293b; padding: 5px; cursor: pointer;">
            <i class="fas fa-bars"></i>
        </button>
        <h1>
            <?= isset($titulo) ? $titulo : 'Dashboard' ?>
            <small>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'admin') ?></small>
        </h1>
    </div>
    <div class="user-info">
        <span class="text-muted small"><?= date('d/m/Y H:i') ?></span>
        <div class="avatar">
            <?= strtoupper(substr($_SESSION['usuario_nombre'] ?? 'A', 0, 1)) ?>
        </div>
    </div>
</div>