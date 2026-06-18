<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= htmlspecialchars($repuesto['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #7c3aed; }
        .info-label { font-weight: 500; color: #64748b; font-size: 13px; }
        .info-value { font-size: 16px; color: #0f172a; font-weight: 500; }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #e2e8f0;
        }
        .repuesto-icon-grande {
            font-size: 60px;
            display: block;
            text-align: center;
            padding: 20px;
            border-radius: 16px;
            background: #f8fafc;
            color: #7c3aed;
        }
        .stock-badge-show {
            padding: 5px 18px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 600;
        }
        .stock-badge-show.alto { background: #d1fae5; color: #065f46; }
        .stock-badge-show.medio { background: #fef3c7; color: #92400e; }
        .stock-badge-show.bajo { background: #fce4ec; color: #b71c1c; }
        .stock-badge-show.agotado { background: #fee2e2; color: #991b1b; }
        .precio-show {
            font-size: 24px;
            font-weight: 700;
            color: #059669;
        }
        .orden-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
        }
        .orden-item:last-child { border-bottom: none; }
        .status-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.en_diagnostico { background: #fef3c7; color: #92400e; }
        .status-badge.en_espera_repuestos { background: #fef3c7; color: #92400e; }
        .status-badge.en_reparacion { background: #dbeafe; color: #1e40af; }
        .status-badge.pendiente { background: #fce4ec; color: #b71c1c; }
        .status-badge.entregado { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2>
                    <i class="fas fa-microchip me-2 text-purple"></i>
                    <?= htmlspecialchars($repuesto['nombre']) ?>
                </h2>
                <p class="text-muted small">Detalles del repuesto y su uso en órdenes</p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?controller=repuesto&action=edit&id=<?= $repuesto['id_repuesto'] ?>" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="index.php?controller=repuesto&action=index" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="form-card">
                    <div class="repuesto-icon-grande">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-label">Nombre</div>
                            <div class="info-value"><?= htmlspecialchars($repuesto['nombre']) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Precio Unitario</div>
                            <div class="info-value precio-show">$<?= number_format($repuesto['precio_unitario'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Stock disponible</div>
                            <div class="info-value">
                                <?php 
                                    $stock = $repuesto['stock'];
                                    $claseStock = $stock > 20 ? 'alto' : ($stock > 5 ? 'medio' : ($stock > 0 ? 'bajo' : 'agotado'));
                                    $textoStock = $stock > 20 ? 'Alto' : ($stock > 5 ? 'Medio' : ($stock > 0 ? 'Bajo' : 'Agotado'));
                                ?>
                                <span class="stock-badge-show <?= $claseStock ?>">
                                    <?= $repuesto['stock'] ?> unidades - <?= $textoStock ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Valor total en inventario</div>
                            <div class="info-value">
                                <strong>$<?= number_format($repuesto['precio_unitario'] * $repuesto['stock'], 0, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-card">
                    <h6 class="mb-3">
                        <i class="fas fa-clipboard-list me-2 text-purple"></i> 
                        Órdenes donde se ha usado (<?= count($ordenes) ?>)
                    </h6>
                    <?php if (!empty($ordenes)): ?>
                        <?php foreach ($ordenes as $orden): ?>
                            <div class="orden-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Orden #<?= $orden['id_orden'] ?></strong>
                                        <span class="ms-2">
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($orden['cliente_nombre']) ?>
                                        </span>
                                        <div class="text-muted small">
                                            <i class="fas fa-desktop me-1"></i>
                                            <?= htmlspecialchars($orden['marca'] . ' ' . $orden['modelo']) ?>
                                            <span class="mx-1">|</span>
                                            <i class="fas fa-user-cog me-1"></i>
                                            <?= htmlspecialchars($orden['tecnico_nombre']) ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-info rounded-pill me-1">
                                            <i class="fas fa-cubes me-1"></i> x<?= $orden['cantidad'] ?>
                                        </span>
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
                                        <div class="mt-1">
                                            <span class="text-primary fw-bold">
                                                $<?= number_format($orden['total'], 0, ',', '.') ?>
                                            </span>
                                            <a href="index.php?controller=orden&action=show&id=<?= $orden['id_orden'] ?>" 
                                               class="btn btn-sm btn-outline-primary ms-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-box fa-2x d-block mb-2 opacity-25"></i>
                            Este repuesto no ha sido usado en ninguna orden de servicio
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>