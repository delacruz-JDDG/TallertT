<?php
// controllers/RepuestoController.php

require_once 'models/Repuesto.php';

class RepuestoController {
    private $repuestoModel;

    public function __construct() {
        $this->repuestoModel = new Repuesto();
    }

    // Listar repuestos
    public function index() {
        $repuestos = $this->repuestoModel->getRepuestosConUso();
        $resumen = $this->repuestoModel->getResumenStock();
        require_once 'views/repuestos/index.php';
    }

    // Mostrar formulario para nuevo repuesto
    public function create() {
        require_once 'views/repuestos/form.php';
    }

    // Guardar nuevo repuesto
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
               'precio_unitario' => floatval(str_replace('.', '', str_replace(',', '', $_POST['precio_unitario'] ?? 0))),
                'stock' => intval($_POST['stock'] ?? 0)
            ];

            $errores = $this->validar($data);

            if (empty($errores)) {
                try {
                    $id = $this->repuestoModel->insert($data);
                    $_SESSION['success'] = 'Repuesto creado exitosamente';
                    header('Location: index.php?controller=repuesto&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al crear el repuesto: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=repuesto&action=create');
            exit;
        }
    }

    // Mostrar formulario para editar repuesto
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $repuesto = $this->repuestoModel->getById($id);
        
        if (!$repuesto) {
            $_SESSION['error'] = 'Repuesto no encontrado';
            header('Location: index.php?controller=repuesto&action=index');
            exit;
        }
        
        require_once 'views/repuestos/form.php';
    }

    // Actualizar repuesto
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'precio_unitario' => floatval(str_replace('.', '', str_replace(',', '', $_POST['precio_unitario'] ?? 0))),
                'stock' => intval($_POST['stock'] ?? 0)
            ];

            $errores = $this->validar($data, $id);

            if (empty($errores)) {
                try {
                    $this->repuestoModel->update($id, $data);
                    $_SESSION['success'] = 'Repuesto actualizado exitosamente';
                    header('Location: index.php?controller=repuesto&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al actualizar el repuesto: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=repuesto&action=edit&id=' . $id);
            exit;
        }
    }

    // Eliminar repuesto
    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        // Verificar si ha sido usado en órdenes
        if ($this->repuestoModel->haSidoUsado($id)) {
            $_SESSION['error'] = 'No se puede eliminar el repuesto porque ha sido usado en órdenes de servicio';
            header('Location: index.php?controller=repuesto&action=index');
            exit;
        }
        
        try {
            $this->repuestoModel->delete($id);
            $_SESSION['success'] = 'Repuesto eliminado exitosamente';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar el repuesto: ' . $e->getMessage();
        }
        
        header('Location: index.php?controller=repuesto&action=index');
        exit;
    }

    // Ver detalle del repuesto
   public function show() {
    $id = $_GET['id'] ?? 0;
    $repuesto = $this->repuestoModel->getById($id);
    
    if (!$repuesto) {
        $_SESSION['error'] = 'Repuesto no encontrado';
        header('Location: index.php?controller=repuesto&action=index');
        exit;
    }
    
    // Obtener órdenes donde se ha usado este repuesto
    $sql = "SELECT 
                o.id_orden,
                o.fecha_recepcion,
                o.estado,
                o.total,
                ro.cantidad,
                c.nombre as cliente_nombre,
                e.marca,
                e.modelo,
                t.nombre as tecnico_nombre
            FROM orden_repuestos ro
            INNER JOIN ordenes_servicio o ON ro.id_orden = o.id_orden
            INNER JOIN equipos e ON o.id_equipo = e.id_equipo
            INNER JOIN clientes c ON e.id_cliente = c.id_cliente
            INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
            WHERE ro.id_repuesto = ?
            ORDER BY o.fecha_recepcion DESC";
    
    // Usar getDB() en lugar de $this->repuestoModel->db
    $ordenes = getDB()->getAll($sql, [$id]);
    
    $titulo = 'Detalle Repuesto';
    require_once 'views/repuestos/show.php';
}

    // Buscar repuestos (AJAX)
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $repuestos = $this->repuestoModel->buscar($termino);
        header('Content-Type: application/json');
        echo json_encode($repuestos);
        exit;
    }

    // Obtener repuestos disponibles (AJAX)
    public function getDisponibles() {
        $repuestos = $this->repuestoModel->getDisponibles();
        header('Content-Type: application/json');
        echo json_encode($repuestos);
        exit;
    }

    // Ajustar stock (sumar o restar)
    public function ajustarStock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $cantidad = intval($_POST['cantidad'] ?? 0);
            $operacion = $_POST['operacion'] ?? 'sumar';
            
            if ($cantidad <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor a 0']);
                exit;
            }
            
            try {
                $this->repuestoModel->actualizarStock($id, $cantidad, $operacion);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    // Validar datos del repuesto
    private function validar($data, $id = null) {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores['nombre'] = 'El nombre del repuesto es obligatorio';
        } elseif (strlen($data['nombre']) < 3) {
            $errores['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if ($data['precio_unitario'] <= 0) {
            $errores['precio_unitario'] = 'El precio debe ser mayor a 0';
        }

        if ($data['stock'] < 0) {
            $errores['stock'] = 'El stock no puede ser negativo';
        }

        return $errores;
    }
}
?>