<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - Reporte por Técnico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #059669; }
        .total-ingresos { font-size: 28px; font-weight: 700; color: #059669; }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2><i class="fas fa-chart-bar me-2 text-success"></i> Reporte de Ingresos por Técnico</h2>
                <p class="text-muted small">Consulta los ingresos generados por cada técnico</p>
            </div>
            <a href="index.php?controller=orden&action=index" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <div class="form-card mb-4">
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="controller" value="orden">
                <input type="hidden" name="action" value="reporteTecnico">
                
                <div class="col-md-4">
                    <label class="form-label">Técnico</label>
                    <select name="id_tecnico" class="form-select" required>
                        <option value="">Seleccionar técnico...</option>
                        <?php foreach ($tecnicos as $tecnico): ?>
                            <option value="<?= $tecnico['id_tecnico'] ?>" 
                                <?= ($_GET['id_tecnico'] ?? '') == $tecnico['id_tecnico'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tecnico['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" 
                           value="<?= $_GET['fecha_inicio'] ?? date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control" 
                           value="<?= $_GET['fecha_fin'] ?? date('Y-m-d') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Consultar
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($reporte)): ?>
            <div class="form-card mb-4">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="text-muted">Total de órdenes</div>
                        <div class="h3"><?= count($reporte) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Total de repuestos usados</div>
                        <div class="h3">
                            <?php 
                                $total_repuestos = 0;
                                foreach ($reporte as $r) {
                                    $total_repuestos += $r['total_repuestos'];
                                }
                                echo $total_repuestos;
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Total de ingresos</div>
                        <div class="total-ingresos">$<?= number_format($total_ingresos, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th># Orden</th>
                                <th>Cliente</th>
                                <th>Equipo</th>
                                <th>Repuestos</th>
                                <th class="text-end">Total</th>
                                <th>Fecha entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reporte as $item): ?>
                                <tr>
                                    <td><strong>#<?= $item['id_orden'] ?></strong></td>
                                    <td><?= htmlspecialchars($item['cliente_nombre']) ?></td>
                                    <td><?= htmlspecialchars($item['marca'] . ' ' . $item['modelo']) ?></td>
                                    <td><?= $item['total_repuestos'] ?></td>
                                    <td class="text-end fw-bold text-primary">
                                        $<?= number_format($item['total'], 0, ',', '.') ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($item['fecha_entrega'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($_GET['id_tecnico'] ?? 0 > 0): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                No hay órdenes entregadas para este técnico en el período seleccionado
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>