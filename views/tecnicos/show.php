<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= htmlspecialchars($tecnico['nombre']) ?></title>
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
        .badge-estado-show {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .badge-estado-show.activo { background: #d1fae5; color: #065f46; }
        .badge-estado-show.inactivo { background: #fce4ec; color: #b71c1c; }
        .badge-especialidad-show {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
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
                    <i class="fas fa-user-circle me-2 text-purple"></i>
                    <?= htmlspecialchars($tecnico['nombre']) ?>
                </h2>
                <p class="text-muted small">Detalles del técnico y sus órdenes asignadas</p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?controller=tecnico&action=edit&id=<?= $tecnico['id_tecnico'] ?>" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="index.php?controller=tecnico&action=index" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="form-card">
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2 text-purple"></i> Información</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-label">Nombre</div>
                            <div class="info-value"><?= htmlspecialchars($tecnico['nombre']) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Especialidad</div>
                            <div class="info-value">
                                <span class="badge-especialidad-show">
                                    <i class="fas fa-tools me-1"></i>
                                    <?= htmlspecialchars($tecnico['especialidad'] ?? 'Sin especialidad') ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Teléfono</div>
                            <div class="info-value"><a href="tel:<?= $tecnico['telefono'] ?>"><?= htmlspecialchars($tecnico['telefono']) ?></a></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                <span class="badge-estado-show <?= $tecnico['estado'] ?>">
                                    <?= ucfirst($tecnico['estado']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-card">
                    <h6 class="mb-3">
                        <i class="fas fa-clipboard-list me-2 text-purple"></i> 
                        Órdenes Asignadas (<?= count($ordenes) ?>)
                    </h6>
                    <?php if (!empty($ordenes)): ?>
                        <?php foreach ($ordenes as $orden): ?>
                            <div class="orden-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>#<?= $orden['id_orden'] ?></strong>
                                        <span class="ms-2">
                                            <?= htmlspecialchars($orden['cliente_nombre']) ?>
                                        </span>
                                        <div class="text-muted small">
                                            <i class="fas fa-desktop me-1"></i>
                                            <?= htmlspecialchars($orden['marca'] . ' ' . $orden['modelo']) ?>
                                            <span class="mx-1">|</span>
                                            <i class="fas fa-barcode me-1"></i>
                                            <?= htmlspecialchars($orden['serial']) ?>
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
                                        <div class="text-muted small mt-1">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?= date('d/m/Y', strtotime($orden['fecha_recepcion'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-clipboard-list fa-2x d-block mb-2 opacity-25"></i>
                            Este técnico no tiene órdenes asignadas
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