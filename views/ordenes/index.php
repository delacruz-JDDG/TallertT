<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - Órdenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #3b82f6; }
        .table-container {
            max-height: 450px;
            overflow-y: auto;
        }
        .table-container thead {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2><i class="fas fa-clipboard-list me-2 text-primary"></i> Órdenes de Servicio</h2>
                <p class="text-muted small">Gestión de órdenes de servicio</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <div class="search-box">
                    <input type="text" id="searchOrden" class="form-control form-control-sm" 
                           placeholder="Buscar orden..." onkeyup="buscarOrden(this.value)">
                </div>
                <a href="index.php?controller=orden&action=create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Nueva Orden
                </a>
                <a href="index.php?controller=orden&action=reporteTecnico" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-chart-bar me-1"></i> Reportes
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

        <!-- Estadísticas -->
        <div class="stats-ordenes">
            <div class="stat-item">
                <div class="number text-primary"><?= $estadisticas['total'] ?? 0 ?></div>
                <div class="label">Total</div>
            </div>
            <div class="stat-item">
                <div class="number text-warning"><?= $estadisticas['en_diagnostico'] ?? 0 ?></div>
                <div class="label">En diagnóstico</div>
            </div>
            <div class="stat-item">
                <div class="number text-info"><?= $estadisticas['en_reparacion'] ?? 0 ?></div>
                <div class="label">En reparación</div>
            </div>
            <div class="stat-item">
                <div class="number text-danger"><?= $estadisticas['pendientes'] ?? 0 ?></div>
                <div class="label">Pendientes</div>
            </div>
            <div class="stat-item">
                <div class="number text-success"><?= $estadisticas['entregadas'] ?? 0 ?></div>
                <div class="label">Entregadas</div>
            </div>
            <div class="stat-item">
                <div class="number text-success">$<?= number_format($estadisticas['total_ingresos'] ?? 0, 0, ',', '.') ?></div>
                <div class="label">Ingresos</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row g-2 mb-3">
            <div class="col-auto">
                <form action="index.php" method="GET" class="d-flex gap-2 flex-wrap">
                    <input type="hidden" name="controller" value="orden">
                    <input type="hidden" name="action" value="index">
                    
                    <select name="filtro" class="form-select form-select-sm filter-box" onchange="this.form.submit()">
                        <option value="">Filtrar por...</option>
                        <option value="estado" <?= ($_GET['filtro'] ?? '') == 'estado' ? 'selected' : '' ?>>Estado</option>
                        <option value="tecnico" <?= ($_GET['filtro'] ?? '') == 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                        <option value="cliente" <?= ($_GET['filtro'] ?? '') == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                    </select>
                    
                    <select name="valor" class="form-select form-select-sm filter-box" onchange="this.form.submit()">
                        <option value="">Seleccionar...</option>
                        <?php if (($_GET['filtro'] ?? '') == 'estado'): ?>
                            <option value="en_diagnostico" <?= ($_GET['valor'] ?? '') == 'en_diagnostico' ? 'selected' : '' ?>>En diagnóstico</option>
                            <option value="en_espera_repuestos" <?= ($_GET['valor'] ?? '') == 'en_espera_repuestos' ? 'selected' : '' ?>>En espera de repuestos</option>
                            <option value="en_reparacion" <?= ($_GET['valor'] ?? '') == 'en_reparacion' ? 'selected' : '' ?>>En reparación</option>
                            <option value="pendiente" <?= ($_GET['valor'] ?? '') == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="entregado" <?= ($_GET['valor'] ?? '') == 'entregado' ? 'selected' : '' ?>>Entregado</option>
                        <?php elseif (($_GET['filtro'] ?? '') == 'tecnico'): ?>
                            <?php foreach ($tecnicos as $tecnico): ?>
                                <option value="<?= $tecnico['id_tecnico'] ?>" <?= ($_GET['valor'] ?? '') == $tecnico['id_tecnico'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tecnico['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php elseif (($_GET['filtro'] ?? '') == 'cliente'): ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>" <?= ($_GET['valor'] ?? '') == $cliente['id_cliente'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cliente['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    
                    <button type="submit" class="btn btn-sm btn-outline-primary">Filtrar</button>
                    <a href="index.php?controller=orden&action=index" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                </form>
            </div>
        </div>

        <!-- Tabla de Órdenes -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaOrdenes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Técnico</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ordenes)): ?>
                            <?php foreach ($ordenes as $orden): ?>
                                <tr>
                                    <td><strong>#<?= $orden['id_orden'] ?></strong></td>
                                    <td><?= htmlspecialchars($orden['cliente_nombre']) ?></td>
                                    <td>
                                        <small><?= htmlspecialchars($orden['marca'] . ' ' . $orden['modelo']) ?></small>
                                        <br><code class="small"><?= htmlspecialchars($orden['serial']) ?></code>
                                    </td>
                                    <td><?= htmlspecialchars($orden['tecnico_nombre']) ?></td>
                                    <td>
                                        <span class="status-badge <?= str_replace(' ', '_', $orden['estado']) ?>">
                                            <?php
                                                $estados = [
                                                    'en_diagnostico' => 'En diagnóstico',
                                                    'en_espera_repuestos' => 'En espera',
                                                    'en_reparacion' => 'En reparación',
                                                    'pendiente' => 'Pendiente',
                                                    'entregado' => 'Entregado'
                                                ];
                                                echo $estados[$orden['estado']] ?? $orden['estado'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-primary">$<?= number_format($orden['total'], 0, ',', '.') ?></strong>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($orden['fecha_recepcion'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="index.php?controller=orden&action=show&id=<?= $orden['id_orden'] ?>" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?controller=orden&action=edit&id=<?= $orden['id_orden'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($orden['estado'] != 'entregado'): ?>
                                                <button onclick="eliminarOrden(<?= $orden['id_orden'] ?>, <?= $orden['id_orden'] ?>)" 
                                                        class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="No se puede eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-clipboard-list fa-2x d-block mb-2 opacity-25"></i>
                                    No hay órdenes de servicio
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación Eliminar -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Confirmar</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de eliminar la orden <strong id="ordenIdEliminar"></strong>?</p>
                    <p class="text-muted small">Los repuestos serán devueltos al stock.</p>
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
        function eliminarOrden(id, numero) {
            document.getElementById('ordenIdEliminar').textContent = '#' + numero;
            document.getElementById('btnEliminarConfirm').href = 
                'index.php?controller=orden&action=delete&id=' + id;
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        }

        function buscarOrden(valor) {
            const tabla = document.getElementById('tablaOrdenes');
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