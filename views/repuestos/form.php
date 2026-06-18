<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= isset($repuesto) ? 'Editar' : 'Nuevo' ?> Repuesto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #7c3aed; }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            max-width: 600px;
        }
        .form-card .form-control,
        .form-card .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
        }
        .form-card .form-control:focus,
        .form-card .form-select:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
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
        .input-group-text {
            border: 2px solid #e2e8f0;
            border-right: none;
            background: #f8fafc;
        }
        .preview-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 2px dashed #e2e8f0;
        }
        .preview-card .icon-big {
            font-size: 48px;
            color: #7c3aed;
        }
        .preview-card .info-preview .label {
            font-size: 12px;
            color: #64748b;
        }
        .preview-card .info-preview .value {
            font-weight: 600;
            font-size: 18px;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2>
                    <i class="fas <?= isset($repuesto) ? 'fa-edit' : 'fa-plus-circle' ?> me-2 text-purple"></i>
                    <?= isset($repuesto) ? 'Editar Repuesto' : 'Nuevo Repuesto' ?>
                </h2>
                <p class="text-muted small"><?= isset($repuesto) ? 'Actualiza los datos del repuesto' : 'Registra un nuevo repuesto en el inventario' ?></p>
            </div>
            <a href="index.php?controller=repuesto&action=index" class="btn btn-outline-secondary btn-sm">
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

        <div class="form-card">
            <form action="index.php?controller=repuesto&action=<?= isset($repuesto) ? 'update' : 'store' ?>" 
                  method="POST" id="formRepuesto">
                
                <?php if (isset($repuesto)): ?>
                    <input type="hidden" name="id" value="<?= $repuesto['id_repuesto'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="nombre" class="form-label required">Nombre del repuesto</label>
                        <input type="text" name="nombre" id="nombre" 
                               class="form-control <?= isset($_SESSION['errores']['nombre']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($_SESSION['old']['nombre'] ?? $repuesto['nombre'] ?? '') ?>"
                               placeholder="Ej: Batería iPhone 13" 
                               oninput="actualizarPreview()" required>
                        <?php if (isset($_SESSION['errores']['nombre'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['nombre'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="precio_unitario" class="form-label required">Precio Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" name="precio_unitario" id="precio_unitario" 
                                   class="form-control <?= isset($_SESSION['errores']['precio_unitario']) ? 'is-invalid' : '' ?>"
                                   value="<?= number_format($_SESSION['old']['precio_unitario'] ?? $repuesto['precio_unitario'] ?? 0, 0, ',', '.') ?>"
                                   placeholder="0" 
                                   oninput="formatearPrecio(this); actualizarPreview()" required>
                        </div>
                        <?php if (isset($_SESSION['errores']['precio_unitario'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['precio_unitario'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="stock" class="form-label required">Stock inicial</label>
                        <input type="number" name="stock" id="stock" 
                               class="form-control <?= isset($_SESSION['errores']['stock']) ? 'is-invalid' : '' ?>"
                               value="<?= $_SESSION['old']['stock'] ?? $repuesto['stock'] ?? 0 ?>"
                               min="0" 
                               oninput="actualizarPreview()" required>
                        <?php if (isset($_SESSION['errores']['stock'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['stock'] ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Cantidad inicial en inventario</small>
                    </div>

                    <div class="col-12">
                        <div class="preview-card" id="previewCard">
                            <div class="icon-big">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="info-preview">
                                <div class="label">Vista previa</div>
                                <div class="value" id="previewNombre">Nombre del repuesto</div>
                                <div>
                                    <span class="badge bg-success" id="previewPrecio">$0</span>
                                    <span class="badge bg-secondary" id="previewStock">0 unidades</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas <?= isset($repuesto) ? 'fa-save' : 'fa-plus' ?> me-2"></i>
                            <?= isset($repuesto) ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="index.php?controller=repuesto&action=index" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formatearPrecio(input) {
            let valor = input.value.replace(/[^0-9]/g, '');
            if (valor) {
                input.value = new Intl.NumberFormat('es-CO').format(parseInt(valor));
            }
        }

        function actualizarPreview() {
            const nombre = document.getElementById('nombre').value || 'Nombre del repuesto';
            const precio = document.getElementById('precio_unitario').value || '0';
            const stock = document.getElementById('stock').value || '0';
            
            document.getElementById('previewNombre').textContent = nombre;
            document.getElementById('previewPrecio').textContent = '$' + precio;
            document.getElementById('previewStock').textContent = stock + ' unidades';
        }

        document.addEventListener('DOMContentLoaded', function() {
            actualizarPreview();
            
            const precioInput = document.getElementById('precio_unitario');
            if (precioInput.value && !isNaN(precioInput.value.replace(/[^0-9]/g, ''))) {
                const valor = precioInput.value.replace(/[^0-9]/g, '');
                if (valor) {
                    precioInput.value = new Intl.NumberFormat('es-CO').format(parseInt(valor));
                }
            }
        });
    </script>

    <?php unset($_SESSION['old']); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>