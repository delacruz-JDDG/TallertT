<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ===== ESTADÍSTICAS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-card .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-card .stat-header .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-card .stat-header .stat-icon.blue { background: #e0e7ff; color: #4f46e5; }
        .stat-card .stat-header .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-card .stat-header .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-card .stat-header .stat-icon.orange { background: #fef3c7; color: #d97706; }

        .stat-card .stat-number {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 4px;
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: #64748b;
        }

        .stat-card .stat-change {
            font-size: 12px;
            font-weight: 600;
            margin-top: 4px;
        }

        .stat-card .stat-change.positive { color: #059669; }
        .stat-card .stat-change.negative { color: #dc2626; }

        /* ===== CONTENIDO PRINCIPAL ===== */
        .dashboard-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card-custom {
            background: white;
            border-radius: 12px;
            padding: 18px;
            border: 1px solid #e2e8f0;
        }

        .card-custom h6 {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
            font-size: 14px;
        }

        /* ===== TABLA DE ÓRDENES RECIENTES ===== */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #e2e8f0;
            overflow-x: auto;
            max-height: 280px;
            overflow-y: auto;
        }

        .table-container table {
            margin-bottom: 0;
            font-size: 13px;
        }

        .table-container table th {
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }

        .table-container table td {
            vertical-align: middle;
            padding: 6px 10px;
        }

        /* ===== GRÁFICOS ===== */
        .chart-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chart-wrapper canvas {
            max-height: 140px !important;
            height: 140px !important;
            width: 100% !important;
        }

        .chart-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 8px;
            font-size: 13px;
        }

        .chart-stats .stat-item {
            text-align: center;
        }

        .chart-stats .stat-item .number {
            font-size: 18px;
            font-weight: 700;
        }

        .chart-stats .stat-item .label {
            color: #64748b;
            font-size: 11px;
        }

        /* ===== ACTIVIDAD RECIENTE ===== */
        .activity-container {
            max-height: 280px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item .activity-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .activity-item .activity-icon.blue { background: #e0e7ff; color: #4f46e5; }
        .activity-item .activity-icon.green { background: #d1fae5; color: #059669; }
        .activity-item .activity-icon.orange { background: #fef3c7; color: #d97706; }

        .activity-item .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-item .activity-content .title {
            font-weight: 500;
            color: #0f172a;
            font-size: 13px;
        }

        .activity-item .activity-content .desc {
            font-size: 12px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .activity-item .activity-content .time {
            font-size: 11px;
            color: #94a3b8;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .dashboard-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
            .stat-card {
                padding: 12px 15px;
            }
            .stat-card .stat-number {
                font-size: 20px;
            }
            .table-container {
                max-height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <!-- ===== ESTADÍSTICAS ===== -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-label">Órdenes Activas</div>
                        <div class="stat-number"><?= $data['ordenes_activas'] ?? 0 ?></div>
                    </div>
                    <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> <?= $data['cambio_ordenes'] ?? '+0' ?>%
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-label">Clientes Registrados</div>
                        <div class="stat-number"><?= $data['total_clientes'] ?? 0 ?></div>
                    </div>
                    <div class="stat-icon green"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> <?= $data['cambio_clientes'] ?? '+0' ?>%
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-label">Técnicos Activos</div>
                        <div class="stat-number"><?= $data['tecnicos_activos'] ?? 0 ?></div>
                    </div>
                    <div class="stat-icon purple"><i class="fas fa-user-cog"></i></div>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> <?= $data['cambio_tecnicos'] ?? '+0' ?>%
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-label">Repuestos en Stock</div>
                        <div class="stat-number"><?= $data['stock_repuestos'] ?? 0 ?></div>
                    </div>
                    <div class="stat-icon orange"><i class="fas fa-microchip"></i></div>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> <?= $data['cambio_repuestos'] ?? '+0' ?>%
                </div>
            </div>
        </div>

        <!-- ===== FILA: ÓRDENES RECIENTES ===== -->
        <div class="card-custom" style="margin-bottom: 20px;">
            <h6><i class="fas fa-clock me-2"></i> Órdenes Recientes</h6>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['ordenes_recientes'])): ?>
                            <?php foreach ($data['ordenes_recientes'] as $orden): ?>
                                <tr>
                                    <td><strong>#<?= $orden['id_orden'] ?></strong></td>
                                    <td><?= htmlspecialchars($orden['cliente_nombre']) ?></td>
                                    <td><?= htmlspecialchars($orden['marca'] . ' ' . $orden['modelo']) ?></td>
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
                                    <td><strong>$<?= number_format($orden['total'], 0, ',', '.') ?></strong></td>
                                    <td>
                                        <a href="index.php?controller=orden&action=show&id=<?= $orden['id_orden'] ?>" 
                                           class="btn btn-sm btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?controller=orden&action=edit&id=<?= $orden['id_orden'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-inbox me-2"></i> No hay órdenes registradas
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== FILA: GRÁFICOS ===== -->
        <div class="dashboard-row">
            <!-- GRÁFICO 1: DONA - Órdenes por Estado -->
            <div class="card-custom">
                <h6><i class="fas fa-chart-pie me-2"></i> Órdenes por Estado</h6>
                <div class="chart-wrapper">
                    <canvas id="chartOrdenes"></canvas>
                    <div class="chart-stats">
                        <?php 
                            $total_ordenes = $data['total_ordenes'] ?? 0;
                            if (!empty($data['datos_grafico'])) {
                                foreach ($data['datos_grafico'] as $item) {
                                    $porcentaje = $total_ordenes > 0 ? round(($item['cantidad'] / $total_ordenes) * 100) : 0;
                                    $label = str_replace('_', ' ', $item['estado']);
                                    $label = str_replace('en ', '', $label);
                                    echo '<div class="stat-item">';
                                    echo '<div class="number" style="font-size:14px; color:' . 
                                        ($item['estado'] == 'entregado' ? '#10b981' : 
                                        ($item['estado'] == 'pendiente' ? '#ef4444' : 
                                        ($item['estado'] == 'en_reparacion' ? '#3b82f6' : '#f59e0b'))) . '">';
                                    echo $item['cantidad'];
                                    echo '</div>';
                                    echo '<div class="label">' . ucfirst($label) . ' (' . $porcentaje . '%)</div>';
                                    echo '</div>';
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>

            <!-- GRÁFICO 2: BARRAS - Resumen de Órdenes -->
            <div class="card-custom">
                <h6><i class="fas fa-chart-bar me-2"></i> Resumen de Órdenes</h6>
                <div class="chart-wrapper">
                    <canvas id="chartResumen"></canvas>
                    <div class="chart-stats" style="margin-top: 8px;">
                        <div class="stat-item">
                            <div class="number" style="font-size:18px; color:#3b82f6;"><?= $data['ordenes_7dias'] ?? 0 ?></div>
                            <div class="label">Últimos 7 días</div>
                        </div>
                        <div class="stat-item">
                            <div class="number" style="font-size:18px; color:#10b981;"><?= $data['total_ordenes'] ?? 0 ?></div>
                            <div class="label">Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SCRIPTS ===== -->
    <script>
    <?php 
        $estados = [];
        $cantidades = [];
        $colores = [];
        
        if (!empty($data['datos_grafico'])) {
            foreach ($data['datos_grafico'] as $item) {
                $estados[] = str_replace('_', ' ', $item['estado']);
                $estados[count($estados)-1] = str_replace('en ', '', $estados[count($estados)-1]);
                $cantidades[] = $item['cantidad'];
                
                switch ($item['estado']) {
                    case 'en_diagnostico': $colores[] = '#f59e0b'; break;
                    case 'en_reparacion': $colores[] = '#3b82f6'; break;
                    case 'pendiente': $colores[] = '#ef4444'; break;
                    case 'entregado': $colores[] = '#10b981'; break;
                    default: $colores[] = '#8b5cf6';
                }
            }
        }
    ?>

    // GRÁFICO 1: DONA - Órdenes por Estado
    new Chart(document.getElementById('chartOrdenes').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($estados) ?>,
            datasets: [{
                data: <?= json_encode($cantidades) ?>,
                backgroundColor: <?= json_encode($colores) ?>,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 8,
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    // GRÁFICO 2: BARRAS HORIZONTALES - Resumen de Órdenes
    new Chart(document.getElementById('chartResumen').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Últimos 7 días', 'Total'],
            datasets: [{
                data: [
                    <?= $data['ordenes_7dias'] ?? 0 ?>, 
                    <?= $data['total_ordenes'] ?? 0 ?>
                ],
                backgroundColor: ['#3b82f6', '#10b981'],
                borderRadius: 6,
                barThickness: 30
            }]
        },
        options: {
            indexAxis: 'y',  // ← BARRAS HORIZONTALES
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 }
                    }
                },
                y: {
                    ticks: {
                        font: { size: 12 }
                    }
                }
            }
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>