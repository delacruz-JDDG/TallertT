<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - Orden #<?= $orden['id_orden'] ?></title>
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
        .status-badge {
            padding: 5px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-badge.en_diagnostico { background: #fef3c7; color: #92400e; }
        .status-badge.en_espera_repuestos { background: #fef3c7; color: #92400e; }
        .status-badge.en_reparacion { background: #dbeafe; color: #1e40af; }
        .status-badge.pendiente { background: #fce4ec; color: #b71c1c; }
        .status-badge.entregado { background: #d1fae5; color: #065f46; }
        .total-grande {
            font-size: 32px;
            font-weight: 700;
            color: #059669;
        }
        .badge-especialidad-show {
            background: #fef3c7;
            color: #92400e;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 13px;
        }
        .repuesto-item {
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .repuesto-item:last-child { border-bottom: none; }
        .print-btn {
            background: #1e293b;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
        }
        .print-btn:hover { background: #0f172a; color: white; }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2>
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>
                    Orden de Servicio #<?= $orden['id_orden'] ?>
                </h2>
                <p class="text-muted small">
                    <?= date('d/m/Y H:i', strtotime($orden['fecha_recepcion'])) ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?controller=orden&action=edit&id=<?= $orden['id_orden'] ?>" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <button onclick="window.print()" class="print-btn btn-sm">
                    <i class="fas fa-print me-1"></i> Imprimir
                </button>
                <a href="index.php?controller=orden&action=index" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="form-card">
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i> Información de la Orden</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                <span class="status-badge <?= str_replace(' ', '_', $orden['estado']) ?>">
                                    <?php
                                        $estados = [
                                            'en_diagnostico' => 'En diagnóstico',
                                            'en_espera_repuestos' => 'En espera de repuestos',
                                            'en_reparacion' => 'En reparación',
                                            'pendiente' => 'Pendiente',
                                            'entregado' => 'Entregado'
                                        ];
                                        echo $estados[$orden['estado']] ?? $orden['estado'];
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Síntoma</div>
                            <div class="info-value"><?= htmlspecialchars($orden['sintoma']) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Mano de obra</div>
                            <div class="info-value">$<?= number_format($orden['mano_obra'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Total</div>
                            <div class="info-value total-grande">$<?= number_format($orden['total'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="form-card">
                            <h6 class="mb-3"><i class="fas fa-user me-2 text-primary"></i> Cliente</h6>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="info-label">Nombre</div>
                                    <div class="info-value"><?= htmlspecialchars($orden['cliente_nombre']) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Teléfono</div>
                                    <div class="info-value"><?= htmlspecialchars($orden['cliente_telefono']) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Email</div>
                                    <div class="info-value"><?= htmlspecialchars($orden['cliente_email']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-card">
                            <h6 class="mb-3"><i class="fas fa-desktop me-2 text-primary"></i> Equipo</h6>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="info-label">Marca / Modelo</div>
                                    <div class="info-value"><?= htmlspecialchars($orden['marca'] . ' ' . $orden['modelo']) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Serial</div>
                                    <div class="info-value"><code><?= htmlspecialchars($orden['serial']) ?></code></div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Tipo</div>
                                    <div class="info-value"><?= ucfirst($orden['equipo_tipo']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-card">
                            <h6 class="mb-3"><i class="fas fa-user-cog me-2 text-primary"></i> Técnico asignado</h6>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="info-label">Nombre</div>
                                    <div class="info-value"><?= htmlspecialchars($orden['tecnico_nombre']) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Especialidad</div>
                                    <div class="info-value">
                                        <span class="badge-especialidad-show">
                                            <?= htmlspecialchars($orden['especialidad'] ?? 'General') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Teléfono</div>
                                    <div class="info-value"><?= htmlspecialchars($orden['tecnico_telefono'] ?? 'No registrado') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-card">
                    <h6 class="mb-3">
                        <i class="fas fa-microchip me-2 text-primary"></i> 
                        Repuestos utilizados (<?= count($repuestos) ?>)
                    </h6>
                    <?php if (!empty($repuestos)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Repuesto</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">Precio Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $subtotal_repuestos = 0;
                                    foreach ($repuestos as $rep): 
                                        $subtotal_repuestos += $rep['subtotal'];
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($rep['nombre']) ?></td>
                                            <td class="text-end">x<?= $rep['cantidad'] ?></td>
                                            <td class="text-end">$<?= number_format($rep['precio_unitario'], 0, ',', '.') ?></td>
                                            <td class="text-end">$<?= number_format($rep['subtotal'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total repuestos</td>
                                        <td class="text-end">$<?= number_format($subtotal_repuestos, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Mano de obra</td>
                                        <td class="text-end">$<?= number_format($orden['mano_obra'], 0, ',', '.') ?></td>
                                    </tr>
                                    <tr class="fw-bold" style="font-size: 1.2em; color: #059669;">
                                        <td colspan="3" class="text-end">TOTAL</td>
                                        <td class="text-end">$<?= number_format($orden['total'], 0, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-box fa-2x d-block mb-2 opacity-25"></i>
                            No se han usado repuestos en esta orden
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