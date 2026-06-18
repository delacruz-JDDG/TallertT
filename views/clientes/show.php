<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - <?= htmlspecialchars($cliente['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #4f46e5; }
        .info-label { font-weight: 500; color: #64748b; font-size: 13px; }
        .info-value { font-size: 16px; color: #0f172a; font-weight: 500; }
        .badge-tipo-show {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
        }
        .badge-tipo-show.particular { background: #dbeafe; color: #1e40af; }
        .badge-tipo-show.empresa { background: #fef3c7; color: #92400e; }
        .equipo-item { 
            padding: 12px 15px; 
            border-bottom: 1px solid #f1f5f9; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .equipo-item:last-child { border-bottom: none; }
        .equipo-item .badge-equipo { 
            background: #e0e7ff; 
            color: #4f46e5; 
            font-size: 12px; 
            padding: 3px 10px; 
            border-radius: 20px; 
        }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #e2e8f0;
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
                    <i class="fas fa-user-circle me-2 text-primary"></i>
                    <?= htmlspecialchars($cliente['nombre']) ?>
                </h2>
                <p class="text-muted small">Detalles del cliente y sus equipos</p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?controller=cliente&action=edit&id=<?= $cliente['id_cliente'] ?>" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="index.php?controller=cliente&action=index" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="form-card">
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i> Información</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-label">Nombre</div>
                            <div class="info-value"><?= htmlspecialchars($cliente['nombre']) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Email</div>
                            <div class="info-value"><a href="mailto:<?= $cliente['email'] ?>"><?= htmlspecialchars($cliente['email']) ?></a></div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Teléfono</div>
                            <div class="info-value"><a href="tel:<?= $cliente['telefono'] ?>"><?= htmlspecialchars($cliente['telefono']) ?></a></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Tipo</div>
                            <div class="info-value">
                                <span class="badge-tipo-show <?= $cliente['tipo'] ?>">
                                    <?= ucfirst($cliente['tipo']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Dirección</div>
                            <div class="info-value"><?= htmlspecialchars($cliente['direccion'] ?? 'No registrada') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="form-card">
                    <h6 class="mb-3">
                        <i class="fas fa-desktop me-2 text-primary"></i> 
                        Equipos (<?= count($equipos) ?>)
                        <a href="index.php?controller=equipo&action=create&cliente=<?= $cliente['id_cliente'] ?>" 
                           class="btn btn-sm btn-primary float-end">
                            <i class="fas fa-plus"></i> Agregar
                        </a>
                    </h6>
                    <?php if (!empty($equipos)): ?>
                        <?php foreach ($equipos as $equipo): ?>
                            <div class="equipo-item">
                                <div>
                                    <strong><?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></strong>
                                    <div class="text-muted small">
                                        <i class="fas fa-barcode me-1"></i> <?= htmlspecialchars($equipo['serial']) ?>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge-equipo"><?= ucfirst($equipo['tipo']) ?></span>
                                    <a href="index.php?controller=equipo&action=edit&id=<?= $equipo['id_equipo'] ?>" 
                                       class="btn btn-sm btn-outline-primary ms-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-desktop fa-2x d-block mb-2 opacity-25"></i>
                            Este cliente no tiene equipos registrados
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