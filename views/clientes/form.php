<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= isset($cliente) ? 'Editar' : 'Nuevo' ?> Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #4f46e5; }
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
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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
                    <i class="fas <?= isset($cliente) ? 'fa-edit' : 'fa-user-plus' ?> me-2 text-primary"></i>
                    <?= isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente' ?>
                </h2>
                <p class="text-muted small"><?= isset($cliente) ? 'Actualiza los datos del cliente' : 'Registra un nuevo cliente en el sistema' ?></p>
            </div>
            <a href="index.php?controller=cliente&action=index" class="btn btn-outline-secondary btn-sm">
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
            <form action="index.php?controller=cliente&action=<?= isset($cliente) ? 'update' : 'store' ?>" 
                  method="POST">
                
                <?php if (isset($cliente)): ?>
                    <input type="hidden" name="id" value="<?= $cliente['id_cliente'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="nombre" class="form-label required">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" 
                               class="form-control <?= isset($_SESSION['errores']['nombre']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($_SESSION['old']['nombre'] ?? $cliente['nombre'] ?? '') ?>"
                               placeholder="Ej: Juan Pérez" required>
                        <?php if (isset($_SESSION['errores']['nombre'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['nombre'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" name="email" id="email" 
                               class="form-control <?= isset($_SESSION['errores']['email']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($_SESSION['old']['email'] ?? $cliente['email'] ?? '') ?>"
                               placeholder="cliente@email.com" required>
                        <?php if (isset($_SESSION['errores']['email'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['email'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="telefono" class="form-label required">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" 
                               class="form-control <?= isset($_SESSION['errores']['telefono']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($_SESSION['old']['telefono'] ?? $cliente['telefono'] ?? '') ?>"
                               placeholder="3001234567" required>
                        <?php if (isset($_SESSION['errores']['telefono'])): ?>
                            <div class="invalid-feedback"><?= $_SESSION['errores']['telefono'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="tipo" class="form-label">Tipo de cliente</label>
                        <select name="tipo" id="tipo" class="form-select">
                            <option value="particular" <?= (($_SESSION['old']['tipo'] ?? $cliente['tipo'] ?? '') == 'particular') ? 'selected' : '' ?>>
                                Particular
                            </option>
                            <option value="empresa" <?= (($_SESSION['old']['tipo'] ?? $cliente['tipo'] ?? '') == 'empresa') ? 'selected' : '' ?>>
                                Empresa
                            </option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea name="direccion" id="direccion" rows="2" 
                                  class="form-control"
                                  placeholder="Dirección del cliente"><?= htmlspecialchars($_SESSION['old']['direccion'] ?? $cliente['direccion'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas <?= isset($cliente) ? 'fa-save' : 'fa-plus' ?> me-2"></i>
                            <?= isset($cliente) ? 'Actualizar' : 'Guardar' ?>
                        </button>
                        <a href="index.php?controller=cliente&action=index" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php unset($_SESSION['old']); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
</body>
</html>