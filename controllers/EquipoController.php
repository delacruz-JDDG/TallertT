<?php
// controllers/EquipoController.php

require_once 'models/Equipo.php';
require_once 'models/Cliente.php';

class EquipoController {
    private $equipoModel;
    private $clienteModel;

    public function __construct() {
        $this->equipoModel = new Equipo();
        $this->clienteModel = new Cliente();
    }

    // Listar equipos
    public function index() {
        $equipos = $this->equipoModel->getEquiposCompletos();
        $estadisticas = $this->equipoModel->getEstadisticasPorTipo();
        require_once 'views/equipos/index.php';
    }

    // Mostrar formulario para nuevo equipo
    public function create() {
        $clientes = $this->clienteModel->getAll();
        $cliente_selected = $_GET['cliente'] ?? null;
        require_once 'views/equipos/form.php';
    }

    // Guardar nuevo equipo
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_cliente' => $_POST['id_cliente'] ?? 0,
                'marca' => trim($_POST['marca'] ?? ''),
                'modelo' => trim($_POST['modelo'] ?? ''),
                'serial' => trim($_POST['serial'] ?? ''),
                'tipo' => $_POST['tipo'] ?? 'computador'
            ];

            $errores = $this->validar($data);

            if (empty($errores)) {
                try {
                    $id = $this->equipoModel->insert($data);
                    $_SESSION['success'] = 'Equipo creado exitosamente';
                    header('Location: index.php?controller=equipo&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al crear el equipo: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=equipo&action=create');
            exit;
        }
    }

    // Mostrar formulario para editar equipo
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $equipo = $this->equipoModel->getById($id);
        
        if (!$equipo) {
            $_SESSION['error'] = 'Equipo no encontrado';
            header('Location: index.php?controller=equipo&action=index');
            exit;
        }
        
        $clientes = $this->clienteModel->getAll();
        require_once 'views/equipos/form.php';
    }

    // Actualizar equipo
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            
            $data = [
                'id_cliente' => $_POST['id_cliente'] ?? 0,
                'marca' => trim($_POST['marca'] ?? ''),
                'modelo' => trim($_POST['modelo'] ?? ''),
                'serial' => trim($_POST['serial'] ?? ''),
                'tipo' => $_POST['tipo'] ?? 'computador'
            ];

            $errores = $this->validar($data, $id);

            if (empty($errores)) {
                try {
                    $this->equipoModel->update($id, $data);
                    $_SESSION['success'] = 'Equipo actualizado exitosamente';
                    header('Location: index.php?controller=equipo&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al actualizar el equipo: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=equipo&action=edit&id=' . $id);
            exit;
        }
    }

    // Eliminar equipo
    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        // Verificar si tiene órdenes
        if ($this->equipoModel->tieneOrdenes($id)) {
            $_SESSION['error'] = 'No se puede eliminar el equipo porque tiene órdenes asociadas';
            header('Location: index.php?controller=equipo&action=index');
            exit;
        }
        
        try {
            $this->equipoModel->delete($id);
            $_SESSION['success'] = 'Equipo eliminado exitosamente';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar el equipo: ' . $e->getMessage();
        }
        
        header('Location: index.php?controller=equipo&action=index');
        exit;
    }

    // Ver detalle del equipo
    public function show() {
        $id = $_GET['id'] ?? 0;
        $equipo = $this->equipoModel->getById($id);
        
        if (!$equipo) {
            $_SESSION['error'] = 'Equipo no encontrado';
            header('Location: index.php?controller=equipo&action=index');
            exit;
        }
        
        $cliente = $this->clienteModel->getById($equipo['id_cliente']);
        $ordenes = $this->equipoModel->getOrdenesEquipo($id);
        require_once 'views/equipos/show.php';
    }

    // Buscar equipos (AJAX)
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $equipos = $this->equipoModel->buscar($termino);
        header('Content-Type: application/json');
        echo json_encode($equipos);
        exit;
    }

    // Obtener equipos por cliente (AJAX)
    public function getByCliente() {
        $id_cliente = $_GET['id_cliente'] ?? 0;
        $equipos = $this->equipoModel->getEquiposByCliente($id_cliente);
        header('Content-Type: application/json');
        echo json_encode($equipos);
        exit;
    }

    // Validar datos del equipo
    private function validar($data, $id = null) {
        $errores = [];

        if (empty($data['id_cliente']) || $data['id_cliente'] == 0) {
            $errores['id_cliente'] = 'Debe seleccionar un cliente';
        }

        if (empty($data['marca'])) {
            $errores['marca'] = 'La marca es obligatoria';
        } elseif (strlen($data['marca']) < 2) {
            $errores['marca'] = 'La marca debe tener al menos 2 caracteres';
        }

        if (empty($data['modelo'])) {
            $errores['modelo'] = 'El modelo es obligatorio';
        } elseif (strlen($data['modelo']) < 2) {
            $errores['modelo'] = 'El modelo debe tener al menos 2 caracteres';
        }

        if (empty($data['serial'])) {
            $errores['serial'] = 'El número de serie es obligatorio';
        } elseif ($this->equipoModel->serialExiste($data['serial'], $id)) {
            $errores['serial'] = 'El número de serie ya está registrado';
        }

        if (empty($data['tipo'])) {
            $errores['tipo'] = 'Debe seleccionar un tipo de equipo';
        }

        return $errores;
    }
}
?>