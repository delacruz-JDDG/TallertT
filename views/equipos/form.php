<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= isset($equipo) ? 'Editar' : 'Nuevo' ?> Equipo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #3b82f6; }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            max-width: 700px;
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
        .icon-preview {
            font-size: 50px;
            text-align: center;
            padding: 15px;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #e2e8f0;
        }
        .icon-preview i { 
            display: block;
            margin-bottom: 5px;
        }
        .icon-preview .label { font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2>
                    <i class="fas <?= isset($equipo) ? 'fa-edit' : 'fa-plus-circle' ?> me-2 text-primary"></i>
                    <?= isset($equipo) ? 'Editar Equipo' : 'Nuevo Equipo' ?>
                </h2>
                <p class="text-muted small"><?= isset($equipo) ? 'Actualiza los datos del equipo' : 'Registra un nuevo equipo en el sistema' ?></p>
            </div>
            <a href="index.php?controller=equipo&action=index" class="btn btn-outline-secondary btn-sm">
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
            <form action="index.php?controller=equipo&action=<?= isset($equipo) ? 'update' : 'store' ?>" 
                  method="POST">
                
                <?php if (isset($equipo)): ?>
                    <input type="hidden" name="id" value="<?= $equipo['id_equipo'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="id_cliente" class="form-label required">Cliente propietario</label>
                        <select name="id_cliente" id="id_cliente" 
                                class="form-select <?= isset($_SESSION['errores']['id_cliente']) ? 'is-invalid' : '' ?>" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>" 
                                    <?= (($_SESSION['old']['id_cliente'] ?? $equipo['id_cliente'] ?? '') == $cliente['id_cliente']) ? 'selected' : '' ?>
                                    <?= (isset($_GET['cliente']) && $_GET['cliente'] == $cliente['id_cliente']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cliente['nombre']) ?> - <?= htmlspecialchars($cliente['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errores']['id_cliente'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['id_cliente'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="marca" class="form-label required">Marca</label>
                        <input type="text" name="marca" id="marca" 
                               class="form-control <?= isset($_SESSION['errores']['marca']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($_SESSION['old']['marca'] ?? $equipo['marca'] ?? '') ?>"
                               placeholder="Ej: Samsung" required>
                        <?php if (isset($_SESSION['errores']['marca'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['marca'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="modelo" class="form-label required">Modelo</label>
                        <input type="text" name="modelo" id="modelo" 
                               class="form-control <?= isset($_SESSION['errores']['modelo']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($_SESSION['old']['modelo'] ?? $equipo['modelo'] ?? '') ?>"
                               placeholder="Ej: Galaxy S23" required>
                        <?php if (isset($_SESSION['errores']['modelo'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['modelo'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="serial" class="form-label required">Número de Serie</label>
                        <input type="text" name="serial" id="serial" 
                               class="form-control <?= isset($_SESSION['errores']['serial']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($_SESSION['old']['serial'] ?? $equipo['serial'] ?? '') ?>"
                               placeholder="Ej: SN-XXX-001" required>
                        <?php if (isset($_SESSION['errores']['serial'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['serial'] ?></div>
                        <?php endif; ?>
                        <small class="text-muted">El número de serie debe ser único</small>
                    </div>

                    <div class="col-md-6">
                        <label for="tipo" class="form-label required">Tipo de equipo</label>
                        <select name="tipo" id="tipo" 
                                class="form-select <?= isset($_SESSION['errores']['tipo']) ? 'is-invalid' : '' ?>" required>
                            <option value="">Seleccionar tipo...</option>
                            <?php 
                            $tipos = ['computador' => 'Computador', 'celular' => 'Celular', 'tablet' => 'Tablet', 'electrodomestico' => 'Electrodoméstico', 'otro' => 'Otro'];
                            $selected = $_SESSION['old']['tipo'] ?? $equipo['tipo'] ?? '';
                            foreach ($tipos as $valor => $label): 
                            ?>
                                <option value="<?= $valor ?>" <?= $selected == $valor ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errores']['tipo'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['tipo'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="icon-preview" id="iconPreview">
                            <i class="fas fa-laptop fa-2x" style="color: #3b82f6;"></i>
                            <span class="label">Vista previa del tipo</span>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas <?= isset($equipo) ? 'fa-save' : 'fa-plus' ?> me-2"></i>
                            <?= isset($equipo) ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="index.php?controller=equipo&action=index" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('tipo').addEventListener('change', function() {
            const iconos = {
                'computador': { icon: 'fa-laptop', color: '#3b82f6' },
                'celular': { icon: 'fa-mobile-alt', color: '#10b981' },
                'tablet': { icon: 'fa-tablet-alt', color: '#f59e0b' },
                'electrodomestico': { icon: 'fa-tv', color: '#ef4444' },
                'otro': { icon: 'fa-microchip', color: '#8b5cf6' }
            };
            
            const tipo = this.value;
            const preview = document.getElementById('iconPreview');
            const icono = iconos[tipo] || iconos['otro'];
            
            preview.innerHTML = `
                <i class="fas ${icono.icon} fa-2x" style="color: ${icono.color};"></i>
                <span class="label">${this.options[this.selectedIndex].text}</span>
            `;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            if (tipoSelect.value) {
                tipoSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>

    <?php unset($_SESSION['old']); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>