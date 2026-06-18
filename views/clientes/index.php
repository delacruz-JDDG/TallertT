<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TallerTech - Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header h2 i { color: #4f46e5; }
    </style>
</head>
<body>
    <?php include_once 'views/partials/sidebar.php'; ?>

    <div class="main-content">
        <?php include_once 'views/partials/topbar.php'; ?>

        <div class="page-header">
            <div>
                <h2><i class="fas fa-users me-2 text-primary"></i> Clientes</h2>
                <p class="text-muted small">Gestión de clientes del taller</p>
            </div>
            <div class="d-flex gap-2">
                <div class="search-box">
                    <input type="text" id="searchCliente" class="form-control form-control-sm" 
                           placeholder="Buscar cliente..." onkeyup="buscarCliente(this.value)">
                </div>
                <a href="index.php?controller=cliente&action=create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Nuevo Cliente
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaClientes">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Tipo</th>
                            <th class="text-center">Equipos</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($clientes)): ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td>
                                        <span class="cliente-avatar">
                                            <?= strtoupper(substr($cliente['nombre'], 0, 1)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($cliente['nombre']) ?></strong>
                                        <div class="text-muted small"><?= htmlspecialchars($cliente['direccion'] ?? 'Sin dirección') ?></div>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-envelope text-muted me-1"></i> <?= htmlspecialchars($cliente['email']) ?></div>
                                        <div><i class="fas fa-phone text-muted me-1"></i> <?= htmlspecialchars($cliente['telefono']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge-tipo <?= $cliente['tipo'] ?>">
                                            <?= ucfirst($cliente['tipo']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">
                                            <?= $cliente['total_equipos'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="index.php?controller=cliente&action=show&id=<?= $cliente['id_cliente'] ?>" 
                                               class="btn btn-sm btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?controller=cliente&action=edit&id=<?= $cliente['id_cliente'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="eliminarCliente(<?= $cliente['id_cliente'] ?>, '<?= addslashes($cliente['nombre']) ?>')" 
                                                    class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x d-block mb-2 opacity-25"></i>
                                    No hay clientes registrados
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Confirmar</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de eliminar a <strong id="clienteNombreEliminar"></strong>?</p>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="btnEliminarConfirm" class="btn btn-sm btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/menu.js"></script>
    <script>
        function eliminarCliente(id, nombre) {
            document.getElementById('clienteNombreEliminar').textContent = nombre;
            document.getElementById('btnEliminarConfirm').href = 
                'index.php?controller=cliente&action=delete&id=' + id;
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        }

        function buscarCliente(valor) {
            const tabla = document.getElementById('tablaClientes');
            const filas = tabla.getElementsByTagName('tr');
            valor = valor.toLowerCase();
            for (let i = 1; i < filas.length; i++) {
                const texto = filas[i].textContent.toLowerCase();
                filas[i].style.display = texto.includes(valor) ? '' : 'none';
            }
        }
    </script>
</body>
</html>