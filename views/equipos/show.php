<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #3b82f6; }
        .info-label { font-weight: 500; color: #64748b; font-size: 13px; }
        .info-value { font-size: 16px; color: #0f172a; font-weight: 500; }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #e2e8f0;
        }
        .equipo-icon-grande {
            font-size: 60px;
            display: block;
            text-align: center;
            padding: 20px;
            border-radius: 16px;
            background: #f8fafc;
        }
        .equipo-icon-grande.computador { color: #3b82f6; }
        .equipo-icon-grande.celular { color: #10b981; }
        .equipo-icon-grande.tablet { color: #f59e0b; }
        .equipo-icon-grande.electrodomestico { color: #ef4444; }
        .equipo-icon-grande.otro { color: #8b5cf6; }
        .badge-tipo-equipo-show {
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .badge-tipo-equipo-show.computador { background: #dbeafe; color: #1e40af; }
        .badge-tipo-equipo-show.celular { background: #d1fae5; color: #065f46; }
        .badge-tipo-equipo-show.tablet { background: #fef3c7; color: #92400e; }
        .badge-tipo-equipo-show.electrodomestico { background: #fce4ec; color: #b71c1c; }
        .badge-tipo-equipo-show.otro { background: #ede9fe; color: #5b21b6; }
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
                    <i class="fas fa-microchip me-2 text-primary"></i>
                    <?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?>
                </h2>
                <p class="text-muted small">Detalles del equipo y sus órdenes de servicio</p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?controller=orden&action=create&equipo=<?= $equipo['id_equipo'] ?>" 
                   class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i> Nueva Orden
                </a>
                <a href="index.php?controller=equipo&action=edit&id=<?= $equipo['id_equipo'] ?>" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="index.php?controller=equipo&action=index" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="form-card">
                    <div class="equipo-icon-grande <?= $equipo['tipo'] ?? 'otro' ?>">
                        <i class="fas <?= 
                            $equipo['tipo'] == 'computador' ? 'fa-laptop' : 
                            ($equipo['tipo'] == 'celular' ? 'fa-mobile-alt' : 
                            ($equipo['tipo'] == 'tablet' ? 'fa-tablet-alt' : 
                            ($equipo['tipo'] == 'electrodomestico' ? 'fa-tv' : 'fa-microchip'))) 
                        ?>"></i>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-label">Marca / Modelo</div>
                            <div class="info-value"><?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Número de Serie</div>
                            <div class="info-value"><code><?= htmlspecialchars($equipo['serial']) ?></code></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Tipo</div>
                            <div class="info-value">
                                <span class="badge-tipo-equipo-show <?= $equipo['tipo'] ?? 'otro' ?>">
                                    <?= ucfirst($equipo['tipo'] ?? 'Otro') ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Propietario</div>
                            <div class="info-value">
                                <a href="index.php?controller=cliente&action=show&id=<?= $cliente['id_cliente'] ?>" 
                                   class="text-decoration-none">
                                    <i class="fas fa-user me-1"></i>
                                    <?= htmlspecialchars($cliente['nombre']) ?>
                                </a>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-phone me-1"></i> <?= htmlspecialchars($cliente['telefono']) ?>
                                    <br>
                                    <i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($cliente['email']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-card">
                    <h6 class="mb-3">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i> 
                        Órdenes de Servicio (<?= count($ordenes) ?>)
                    </h6>
                    <?php if (!empty($ordenes)): ?>
                        <?php foreach ($ordenes as $orden): ?>
                            <div class="orden-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>#<?= $orden['id_orden'] ?></strong>
                                        <span class="ms-2">
                                            <i class="fas fa-user-cog me-1"></i>
                                            <?= htmlspecialchars($orden['tecnico_nombre']) ?>
                                        </span>
                                        <div class="text-muted small">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($orden['fecha_recepcion'])) ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
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
                                            <strong class="text-primary">$<?= number_format($orden['total_orden'], 0, ',', '.') ?></strong>
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
                            <i class="fas fa-clipboard-list fa-2x d-block mb-2 opacity-25"></i>
                            Este equipo no tiene órdenes de servicio
                            <br>
                            <a href="index.php?controller=orden&action=create&equipo=<?= $equipo['id_equipo'] ?>" 
                               class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-plus me-1"></i> Crear primera orden
                            </a>
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