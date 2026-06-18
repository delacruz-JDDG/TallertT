<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #3b82f6; }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2><i class="fas fa-desktop me-2 text-primary"></i> Equipos</h2>
                <p class="text-muted small">Gestión de equipos de los clientes</p>
            </div>
            <div class="d-flex gap-2">
                <div class="search-box">
                    <input type="text" id="searchEquipo" class="form-control form-control-sm" 
                           placeholder="Buscar equipo..." onkeyup="buscarEquipo(this.value)">
                </div>
                <a href="index.php?controller=equipo&action=create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Nuevo Equipo
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="stats-mini">
            <?php if (!empty($estadisticas)): ?>
                <?php foreach ($estadisticas as $stat): ?>
                    <div class="stat-item">
                        <div class="number"><?= $stat['cantidad'] ?></div>
                        <div class="label">
                            <span class="color-dot" style="background: <?= 
                                $stat['tipo'] == 'computador' ? '#3b82f6' : 
                                ($stat['tipo'] == 'celular' ? '#10b981' : 
                                ($stat['tipo'] == 'tablet' ? '#f59e0b' : 
                                ($stat['tipo'] == 'electrodomestico' ? '#ef4444' : '#8b5cf6'))) 
                            ?>"></span>
                            <?= ucfirst($stat['tipo'] ?? 'Otro') ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaEquipos">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Equipo</th>
                            <th>Serial</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th class="text-center">Órdenes</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($equipos)): ?>
                            <?php foreach ($equipos as $equipo): ?>
                                <tr>
                                    <td>
                                        <span class="equipo-icon <?= $equipo['tipo'] ?? 'otro' ?>">
                                            <i class="fas <?= 
                                                $equipo['tipo'] == 'computador' ? 'fa-laptop' : 
                                                ($equipo['tipo'] == 'celular' ? 'fa-mobile-alt' : 
                                                ($equipo['tipo'] == 'tablet' ? 'fa-tablet-alt' : 
                                                ($equipo['tipo'] == 'electrodomestico' ? 'fa-tv' : 'fa-microchip'))) 
                                            ?>"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></strong>
                                    </td>
                                    <td>
                                        <code class="small"><?= htmlspecialchars($equipo['serial']) ?></code>
                                    </td>
                                    <td>
                                        <a href="index.php?controller=cliente&action=show&id=<?= $equipo['id_cliente'] ?>" 
                                           class="text-decoration-none">
                                            <?= htmlspecialchars($equipo['cliente_nombre']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge-tipo-equipo <?= $equipo['tipo'] ?? 'otro' ?>">
                                            <?= ucfirst($equipo['tipo'] ?? 'Otro') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            <?= $equipo['total_ordenes'] ?? 0 ?>
                                        </span>
                                        <?php if (($equipo['ordenes_activas'] ?? 0) > 0): ?>
                                            <span class="badge bg-warning text-dark rounded-pill">
                                                <?= $equipo['ordenes_activas'] ?> activas
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="index.php?controller=equipo&action=show&id=<?= $equipo['id_equipo'] ?>" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?controller=equipo&action=edit&id=<?= $equipo['id_equipo'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (($equipo['total_ordenes'] ?? 0) == 0): ?>
                                                <button onclick="eliminarEquipo(<?= $equipo['id_equipo'] ?>, '<?= addslashes($equipo['marca'] . ' ' . $equipo['modelo']) ?>')" 
                                                        class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="Tiene órdenes asociadas">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-desktop fa-2x d-block mb-2 opacity-25"></i>
                                    No hay equipos registrados
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Confirmar</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de eliminar el equipo <strong id="equipoNombreEliminar"></strong>?</p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="btnEliminarConfirm" class="btn btn-sm btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
    <script>
        function eliminarEquipo(id, nombre) {
            document.getElementById('equipoNombreEliminar').textContent = nombre;
            document.getElementById('btnEliminarConfirm').href = 
                'index.php?controller=equipo&action=delete&id=' + id;
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        }

        function buscarEquipo(valor) {
            const tabla = document.getElementById('tablaEquipos');
            const filas = tabla.getElementsByTagName('tr');
            valor = valor.toLowerCase();
            for (let i = 1; i < filas.length; i++) {
                const texto = filas[i].textContent.toLowerCase();
                filas[i].style.display = texto.includes(valor) ? '' : 'none';
            }
        }
    </script>
</body>
</html>