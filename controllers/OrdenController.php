<?php
// controllers/OrdenController.php

require_once 'models/Orden.php';
require_once 'models/Equipo.php';
require_once 'models/Tecnico.php';
require_once 'models/Repuesto.php';
require_once 'models/Cliente.php';

class OrdenController {
    private $ordenModel;
    private $equipoModel;
    private $tecnicoModel;
    private $repuestoModel;
    private $clienteModel;

    public function __construct() {
        $this->ordenModel = new Orden();
        $this->equipoModel = new Equipo();
        $this->tecnicoModel = new Tecnico();
        $this->repuestoModel = new Repuesto();
        $this->clienteModel = new Cliente();
    }

    // Listar órdenes
    public function index() {
        $ordenes = $this->ordenModel->getOrdenesCompletas();
        $estadisticas = $this->ordenModel->getEstadisticas();
        $filtro = $_GET['filtro'] ?? '';
        $valor = $_GET['valor'] ?? '';
        
        // Aplicar filtros si existen
        if ($filtro && $valor) {
            switch ($filtro) {
                case 'estado':
                    $ordenes = $this->ordenModel->getByEstado($valor);
                    break;
                case 'tecnico':
                    $ordenes = $this->ordenModel->getByTecnico($valor);
                    break;
                case 'cliente':
                    $ordenes = $this->ordenModel->getByCliente($valor);
                    break;
            }
        }
        
        // Obtener datos para filtros
        $tecnicos = $this->tecnicoModel->getActivos();
        $clientes = $this->clienteModel->getAll();
        
        require_once 'views/ordenes/index.php';
    }

    // Mostrar formulario para nueva orden
    public function create() {
        $equipo_selected = $_GET['equipo'] ?? null;
        
        if ($equipo_selected) {
            $equipo = $this->equipoModel->getById($equipo_selected);
            if ($equipo) {
                $_SESSION['equipo_selected'] = $equipo;
            }
        }
        
        $equipos = $this->equipoModel->getEquiposConCliente();
        $tecnicos = $this->tecnicoModel->getActivos();
        $repuestos = $this->repuestoModel->getDisponibles();
        
        require_once 'views/ordenes/form.php';
    }

    // Guardar nueva orden
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_equipo' => $_POST['id_equipo'] ?? 0,
                'id_tecnico' => $_POST['id_tecnico'] ?? 0,
                'sintoma' => trim($_POST['sintoma'] ?? ''),
                'estado' => $_POST['estado'] ?? 'en_diagnostico', 
                'mano_obra' => floatval(str_replace('.', '', str_replace(',', '', $_POST['mano_obra'] ?? 0))),
                'estado' => $_POST['estado'] ?? 'en_diagnostico'

            ];

            $errores = $this->validar($data);

            if (empty($errores)) {
                try {
                    // Verificar si el equipo tiene orden activa
                    if ($this->ordenModel->equipoTieneOrdenActiva($data['id_equipo'])) {
                        $_SESSION['error'] = 'El equipo ya tiene una orden activa';
                        header('Location: index.php?controller=orden&action=create');
                        exit;
                    }
                    
                    $id = $this->ordenModel->insert($data);
                    
                    // Recalcular total
                    $this->ordenModel->recalcularTotal($id);
                    
                    $_SESSION['success'] = 'Orden creada exitosamente';
                    header('Location: index.php?controller=orden&action=edit&id=' . $id);
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al crear la orden: ' . $e->getMessage();
                    header('Location: index.php?controller=orden&action=create');
                    exit;
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
                header('Location: index.php?controller=orden&action=create');
                exit;
             }
          }
     }
        
        
    // Mostrar formulario para editar orden
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $orden = $this->ordenModel->getOrdenCompleta($id);
        
        if (!$orden) {
            $_SESSION['error'] = 'Orden no encontrada';
            header('Location: index.php?controller=orden&action=index');
            exit;
        }
        
        $repuestos_orden = $this->ordenModel->getRepuestosOrden($id);
        $repuestos_disponibles = $this->repuestoModel->getDisponibles();
        $tecnicos = $this->tecnicoModel->getActivos();
        $equipos = $this->equipoModel->getEquiposConCliente();
        
        require_once 'views/ordenes/form.php';
    }

    // Actualizar orden
    public function update() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? 0;
        
        $orden_actual = $this->ordenModel->getById($id);
        
        $data = [
            'id_equipo' => $_POST['id_equipo'] ?? 0,
            'id_tecnico' => $_POST['id_tecnico'] ?? 0,
            'sintoma' => trim($_POST['sintoma'] ?? ''),
            'mano_obra' => floatval(str_replace('.', '', str_replace(',', '', $_POST['mano_obra'] ?? 0))),
            'estado' => $_POST['estado'] ?? 'en_diagnostico'
        ];

        $errores = $this->validar($data, $id);

        if (empty($errores)) {
            try {
                if ($data['id_equipo'] != $orden_actual['id_equipo']) {
                    if ($this->ordenModel->equipoTieneOrdenActiva($data['id_equipo'], $id)) {
                        $_SESSION['error'] = 'El equipo ya tiene otra orden activa';
                        header('Location: index.php?controller=orden&action=edit&id=' . $id);
                        exit;
                    }
                }
                
                $update_data = [
                    'id_equipo' => $data['id_equipo'],
                    'id_tecnico' => $data['id_tecnico'],
                    'sintoma' => $data['sintoma'],
                    'mano_obra' => $data['mano_obra'],
                    'estado' => $data['estado']
                ];
                
                $this->ordenModel->update($id, $update_data);
                $this->ordenModel->recalcularTotal($id);
                
                $_SESSION['success'] = 'Orden actualizada exitosamente';
                header('Location: index.php?controller=orden&action=edit&id=' . $id);
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar la orden: ' . $e->getMessage();
            }
        } else {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $data;
        }
        
        header('Location: index.php?controller=orden&action=edit&id=' . $id);
        exit;
    }
}

    // Cambiar estado de la orden
    public function cambiarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $estado = $_POST['estado'] ?? '';
            
            // Validar estado
            $estados_validos = ['en_diagnostico', 'en_espera_repuestos', 'en_reparacion', 'pendiente', 'entregado'];
            if (!in_array($estado, $estados_validos)) {
                echo json_encode(['success' => false, 'message' => 'Estado no válido']);
                exit;
            }
            
            try {
                $this->ordenModel->cambiarEstado($id, $estado);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    // Agregar repuesto a la orden
    public function agregarRepuesto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_orden = $_POST['id_orden'] ?? 0;
            $id_repuesto = $_POST['id_repuesto'] ?? 0;
            $cantidad = intval($_POST['cantidad'] ?? 1);
            
            if ($cantidad <= 0) {
                echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor a 0']);
                exit;
            }
            
            try {
                $this->ordenModel->agregarRepuesto($id_orden, $id_repuesto, $cantidad);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    // Eliminar repuesto de la orden
    public function eliminarRepuesto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_orden = $_POST['id_orden'] ?? 0;
            $id_repuesto = $_POST['id_repuesto'] ?? 0;
            
            try {
                $this->ordenModel->eliminarRepuesto($id_orden, $id_repuesto);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    // Ver detalle de la orden
    public function show() {
        $id = $_GET['id'] ?? 0;
        $orden = $this->ordenModel->getOrdenCompleta($id);
        
        if (!$orden) {
            $_SESSION['error'] = 'Orden no encontrada';
            header('Location: index.php?controller=orden&action=edit&id=' . $id);
            exit;
        }
        
        $repuestos = $this->ordenModel->getRepuestosOrden($id);
        require_once 'views/ordenes/show.php';
    }

    // Eliminar orden
    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        // Verificar si la orden está entregada
        $orden = $this->ordenModel->getById($id);
        if ($orden && $orden['estado'] == 'entregado') {
            $_SESSION['error'] = 'No se puede eliminar una orden entregada';
            header('Location: index.php?controller=orden&action=edit&id=' . $id);
            exit;
        }
        
        // Si tiene repuestos, devolverlos al stock
        $repuestos = $this->ordenModel->getRepuestosOrden($id);
        foreach ($repuestos as $repuesto) {
           $this->repuestoModel->actualizarStock($repuesto['id_repuesto'], $repuesto['cantidad'], 'sumar');
    
        }
        
        try {
            $this->ordenModel->delete($id);
            $_SESSION['success'] = 'Orden eliminada exitosamente';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar la orden: ' . $e->getMessage();
        }
        
        header('Location: index.php?controller=orden&action=edit&id=' . $id);
        exit;
    }

    // Reporte de ingresos por técnico
    public function reporteTecnico() {
        $id_tecnico = $_GET['id_tecnico'] ?? 0;
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $tecnicos = $this->tecnicoModel->getActivos();
        $reporte = [];
        $total_ingresos = 0;
        
        if ($id_tecnico > 0) {
            $reporte = $this->ordenModel->reporteIngresosTecnico($id_tecnico, $fecha_inicio, $fecha_fin);
            $total_ingresos = $this->ordenModel->totalIngresosTecnico($id_tecnico, $fecha_inicio, $fecha_fin);
        }
        
        require_once 'views/ordenes/reporte_tecnico.php';
    }

    // Buscar órdenes (AJAX)
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $sql = "SELECT 
                    o.*,
                    c.nombre as cliente_nombre,
                    e.marca,
                    e.modelo,
                    t.nombre as tecnico_nombre
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                WHERE c.nombre LIKE ? 
                    OR e.marca LIKE ? 
                    OR e.modelo LIKE ?
                    OR o.id_orden LIKE ?
                ORDER BY o.fecha_recepcion DESC";
        $param = "%$termino%";
        $ordenes = $this->ordenModel->db->getAll($sql, [$param, $param, $param, $param]);
        
        header('Content-Type: application/json');
        echo json_encode($ordenes);
        exit;
    }

    // Validar datos de la orden
    private function validar($data, $id = null) {
        $errores = [];

        if (empty($data['id_equipo']) || $data['id_equipo'] == 0) {
            $errores['id_equipo'] = 'Debe seleccionar un equipo';
        }

        if (empty($data['id_tecnico']) || $data['id_tecnico'] == 0) {
            $errores['id_tecnico'] = 'Debe seleccionar un técnico';
        }

        // Verificar que el técnico esté activo
        if ($data['id_tecnico'] > 0) {
            $tecnico = $this->tecnicoModel->getById($data['id_tecnico']);
            if ($tecnico && $tecnico['estado'] != 'activo') {
                $errores['id_tecnico'] = 'El técnico seleccionado no está activo';
            }
        }

        if (empty($data['sintoma'])) {
            $errores['sintoma'] = 'Debe describir el síntoma del equipo';
        }

        if ($data['mano_obra'] < 0) {
            $errores['mano_obra'] = 'La mano de obra no puede ser negativa';
        }

        return $errores;
    }
}
?>