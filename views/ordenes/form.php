<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= isset($orden) ? 'Editar' : 'Nueva' ?> Orden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #3b82f6; }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #e2e8f0;
        }
        .form-card .form-control,
        .form-card .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
        }
        .form-card .form-control:focus,
        .form-card .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-card .form-label {
            font-weight: 500;
            color: #1e293b;
        }
        .required::after {
            content: '*';
            color: #dc2626;
            margin-left: 4px;
        }
        .status-badge {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-badge.en_diagnostico { background: #fef3c7; color: #92400e; }
        .status-badge.en_espera_repuestos { background: #fef3c7; color: #92400e; }
        .status-badge.en_reparacion { background: #dbeafe; color: #1e40af; }
        .status-badge.pendiente { background: #fce4ec; color: #b71c1c; }
        .status-badge.entregado { background: #d1fae5; color: #065f46; }
        .total-orden {
            font-size: 24px;
            font-weight: 700;
            color: #059669;
        }
        .repuesto-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f1f5f9;
        }
        .repuesto-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2>
                    <i class="fas <?= isset($orden) ? 'fa-edit' : 'fa-plus-circle' ?> me-2 text-primary"></i>
                    <?= isset($orden) ? 'Editar Orden #' . $orden['id_orden'] : 'Nueva Orden de Servicio' ?>
                </h2>
                <p class="text-muted small">
                    <?= isset($orden) ? 'Actualiza los datos de la orden' : 'Registra una nueva orden de servicio' ?>
                </p>
            </div>
            <a href="index.php?controller=orden&action=index" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <?php if (isset($_SESSION['errores'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i> Por favor corrige los siguientes errores:
                <ul class="mb-0 mt-1">
                    <?php foreach ($_SESSION['errores'] as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errores']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="form-card">
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i> Datos de la Orden</h6>
                    
                    <form action="index.php?controller=orden&action=<?= isset($orden) ? 'update' : 'store' ?>" 
                          method="POST" id="formOrden">
                        
                        <?php if (isset($orden)): ?>
                            <input type="hidden" name="id" value="<?= $orden['id_orden'] ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="id_equipo" class="form-label required">Equipo</label>
                                <select name="id_equipo" id="id_equipo" 
                                        class="form-select <?= isset($_SESSION['errores']['id_equipo']) ? 'is-invalid' : '' ?>" required>
                                    <option value="">Seleccionar equipo...</option>
                                    <?php foreach ($equipos as $equipo): ?>
                                        <option value="<?= $equipo['id_equipo'] ?>" 
                                            <?= (($_SESSION['old']['id_equipo'] ?? $orden['id_equipo'] ?? '') == $equipo['id_equipo']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($equipo['cliente_nombre'] . ' - ' . $equipo['marca'] . ' ' . $equipo['modelo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($_SESSION['errores']['id_equipo'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errores']['id_equipo'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <label for="id_tecnico" class="form-label required">Técnico asignado</label>
                                <select name="id_tecnico" id="id_tecnico" 
                                        class="form-select <?= isset($_SESSION['errores']['id_tecnico']) ? 'is-invalid' : '' ?>" required>
                                    <option value="">Seleccionar técnico...</option>
                                    <?php foreach ($tecnicos as $tecnico): ?>
                                        <option value="<?= $tecnico['id_tecnico'] ?>" 
                                            <?= (($_SESSION['old']['id_tecnico'] ?? $orden['id_tecnico'] ?? '') == $tecnico['id_tecnico']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tecnico['nombre'] . ' - ' . $tecnico['especialidad']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($_SESSION['errores']['id_tecnico'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errores']['id_tecnico'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <label for="sintoma" class="form-label required">Síntoma reportado</label>
                                <textarea name="sintoma" id="sintoma" rows="3" 
                                          class="form-control <?= isset($_SESSION['errores']['sintoma']) ? 'is-invalid' : '' ?>"
                                          placeholder="Describe el problema del equipo..." required><?= htmlspecialchars($_SESSION['old']['sintoma'] ?? $orden['sintoma'] ?? '') ?></textarea>
                                <?php if (isset($_SESSION['errores']['sintoma'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errores']['sintoma'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <label for="mano_obra" class="form-label">Mano de obra</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" name="mano_obra" id="mano_obra" 
                                           class="form-control <?= isset($_SESSION['errores']['mano_obra']) ? 'is-invalid' : '' ?>"
                                           value="<?= number_format($_SESSION['old']['mano_obra'] ?? $orden['mano_obra'] ?? 0, 0, ',', '.') ?>"
                                           placeholder="0" 
                                           oninput="formatearPrecio(this)">
                                </div>
                                <?php if (isset($_SESSION['errores']['mano_obra'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errores']['mano_obra'] ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Valor de la mano de obra del técnico</small>
                            </div>

                            <?php if (isset($orden)): ?>
                                <div class="col-12">
                                    <label class="form-label">Estado actual</label>
                                    <div>
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
                            <?php endif; ?>

                            <div class="col-12 d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas <?= isset($orden) ? 'fa-save' : 'fa-plus' ?> me-2"></i>
                                    <?= isset($orden) ? 'Actualizar Orden' : 'Crear Orden' ?>
                                </button>
                                <a href="index.php?controller=orden&action=index" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($orden)): ?>
                <div class="col-md-6">
                    <div class="form-card">
                        <h6 class="mb-3">
                            <i class="fas fa-microchip me-2 text-primary"></i> 
                            Repuestos de la Orden
                            <span class="badge bg-primary rounded-pill"><?= count($repuestos_orden) ?></span>
                        </h6>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <select id="repuestoSelect" class="form-select form-select-sm">
                                    <option value="">Seleccionar repuesto...</option>
                                    <?php foreach ($repuestos_disponibles as $rep): ?>
                                        <option value="<?= $rep['id_repuesto'] ?>" 
                                                data-precio="<?= $rep['precio_unitario'] ?>"
                                                data-stock="<?= $rep['stock'] ?>">
                                            <?= htmlspecialchars($rep['nombre']) ?> (Stock: <?= $rep['stock'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" id="cantidadRepuesto" class="form-control form-control-sm" 
                                       value="1" min="1" placeholder="Cant">
                            </div>
                            <div class="col-3">
                                <button onclick="agregarRepuesto()" class="btn btn-sm btn-success w-100">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>

                        <div id="listaRepuestos">
                            <?php if (!empty($repuestos_orden)): ?>
                                <?php foreach ($repuestos_orden as $rep): ?>
                                    <div class="repuesto-item" id="repuesto-<?= $rep['id_repuesto'] ?>">
                                        <div>
                                            <strong><?= htmlspecialchars($rep['nombre']) ?></strong>
                                            <div class="text-muted small">
                                                x<?= $rep['cantidad'] ?> × $<?= number_format($rep['precio_unitario'], 0, ',', '.') ?>
                                                = $<?= number_format($rep['subtotal'], 0, ',', '.') ?>
                                            </div>
                                        </div>
                                        <button onclick="eliminarRepuesto(<?= $rep['id_repuesto'] ?>)" 
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center py-3">
                                    <i class="fas fa-box fa-2x d-block mb-2 opacity-25"></i>
                                    No hay repuestos agregados
                                </p>
                            <?php endif; ?>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">Total de la orden</span>
                            </div>
                            <div class="total-orden" id="totalOrden">
                                $<?= number_format($orden['total'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function formatearPrecio(input) {
            let valor = input.value.replace(/[^0-9]/g, '');
            if (valor) {
                input.value = new Intl.NumberFormat('es-CO').format(parseInt(valor));
            }
        }

        function agregarRepuesto() {
            const repuestoSelect = document.getElementById('repuestoSelect');
            const cantidad = document.getElementById('cantidadRepuesto');
            
            if (!repuestoSelect.value) {
                alert('Seleccione un repuesto');
                return;
            }
            
            if (cantidad.value < 1) {
                alert('La cantidad debe ser mayor a 0');
                return;
            }
            
            const data = new FormData();
            data.append('id_orden', <?= $orden['id_orden'] ?? 0 ?>);
            data.append('id_repuesto', repuestoSelect.value);
            data.append('cantidad', cantidad.value);
            
            fetch('index.php?controller=orden&action=agregarRepuesto', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error de conexión');
            });
        }

        function eliminarRepuesto(id_repuesto) {
            if (!confirm('¿Eliminar este repuesto de la orden?')) return;
            
            const data = new FormData();
            data.append('id_orden', <?= $orden['id_orden'] ?? 0 ?>);
            data.append('id_repuesto', id_repuesto);
            
            fetch('index.php?controller=orden&action=eliminarRepuesto', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error de conexión');
            });
        }

        <?php if (isset($orden)): ?>
        function cambiarEstado(estado) {
            if (!confirm('¿Cambiar el estado de la orden a "' + estado + '"?')) return;
            
            const data = new FormData();
            data.append('id', <?= $orden['id_orden'] ?>);
            data.append('estado', estado);
            
            fetch('index.php?controller=orden&action=cambiarEstado', {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error de conexión');
            });
        }
        <?php endif; ?>
    </script>

    <?php 
    unset($_SESSION['old']); 
    unset($_SESSION['equipo_selected']); 
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>