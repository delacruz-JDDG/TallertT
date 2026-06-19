<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= isset($tecnico) ? 'Editar' : 'Nuevo' ?> Técnico</title>
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
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2>
                    <i class="fas <?= isset($tecnico) ? 'fa-edit' : 'fa-user-plus' ?> me-2 text-purple"></i>
                    <?= isset($tecnico) ? 'Editar Técnico' : 'Nuevo Técnico' ?>
                </h2>
                <p class="text-muted small"><?= isset($tecnico) ? 'Actualiza los datos del técnico' : 'Registra un nuevo técnico en el sistema' ?></p>
            </div>
            <a href="index.php?controller=tecnico&action=index" class="btn btn-outline-secondary btn-sm">
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
            <form action="index.php?controller=tecnico&action=<?= isset($tecnico) ? 'update' : 'store' ?>" 
                  method="POST">
                
                <?php if (isset($tecnico)): ?>
                    <input type="hidden" name="id" value="<?= $tecnico['id_tecnico'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="nombre" class="form-label required">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" 
                               class="form-control <?= isset($_SESSION['errores']['nombre']) ? 'is-invalid' : '' ?>"
                               onkeypress="return soloLetras(event)"
                               value="<?= htmlspecialchars($_SESSION['old']['nombre'] ?? $tecnico['nombre'] ?? '') ?>"
                               placeholder="Ej: Carlos Pérez" required>
                        <?php if (isset($_SESSION['errores']['nombre'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['nombre'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="especialidad" class="form-label required">Especialidad</label>
                        <select name="especialidad" id="especialidad" 
                                class="form-select <?= isset($_SESSION['errores']['especialidad']) ? 'is-invalid' : '' ?>" required>
                            <option value="">Seleccionar especialidad...</option>
                            <?php 
                            $especialidades = ['Celulares/Tablets', 'Computadores', 'Electrodomésticos', 'TV/Audio', 'Redes', 'General'];
                            $selected = $_SESSION['old']['especialidad'] ?? $tecnico['especialidad'] ?? '';
                            foreach ($especialidades as $esp): 
                            ?>
                                <option value="<?= $esp ?>" <?= $selected == $esp ? 'selected' : '' ?>>
                                    <?= $esp ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errores']['especialidad'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['especialidad'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="telefono" class="form-label required">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" 
                               class="form-control <?= isset($_SESSION['errores']['telefono']) ? 'is-invalid' : '' ?>"
                               maxlength="15"
                               onkeypress="return soloNumeros(event)"
                               value="<?= htmlspecialchars($_SESSION['old']['telefono'] ?? $tecnico['telefono'] ?? '') ?>"
                               placeholder="3001234567" required>
                        <?php if (isset($_SESSION['errores']['telefono'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['telefono'] ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($tecnico)): ?>
                        <div class="col-12">
                            <label for="estado" class="form-label">Estado</label>
                            <select name="estado" id="estado" class="form-select">
                                <option value="activo" <?= ($tecnico['estado'] ?? '') == 'activo' ? 'selected' : '' ?>>
                                    Activo
                                </option>
                                <option value="inactivo" <?= ($tecnico['estado'] ?? '') == 'inactivo' ? 'selected' : '' ?>>
                                    Inactivo
                                </option>
                            </select>
                            <small class="text-muted">Si el técnico tiene órdenes activas, no se podrá desactivar</small>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="estado" value="activo">
                    <?php endif; ?>

                    <div class="col-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas <?= isset($tecnico) ? 'fa-save' : 'fa-plus' ?> me-2"></i>
                            <?= isset($tecnico) ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="index.php?controller=tecnico&action=index" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php unset($_SESSION['old']); ?>

    <script>
    function soloLetras(e) {
        var key = e.keyCode || e.which;
        var tecla = String.fromCharCode(key);
        var permitidas = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/;
        return permitidas.test(tecla);
    }

    function soloNumeros(e) {
        var key = e.keyCode || e.which;
        var tecla = String.fromCharCode(key);
        var numeros = /^[0-9]$/;
        return numeros.test(tecla);
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>