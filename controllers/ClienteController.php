<?php
// controllers/ClienteController.php

require_once 'models/Cliente.php';

class ClienteController {
    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    // Listar clientes
    public function index() {
        $clientes = $this->clienteModel->getClientesConEquipos();
        require_once 'views/clientes/index.php';
    }

    // Mostrar formulario para nuevo cliente
    public function create() {
        require_once 'views/clientes/form.php';
    }

    // Guardar nuevo cliente
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'tipo' => $_POST['tipo'] ?? 'particular'
            ];

            // Validaciones
            $errores = $this->validar($data);

            if (empty($errores)) {
                try {
                    $id = $this->clienteModel->insert($data);
                    $_SESSION['success'] = 'Cliente creado exitosamente';
                    header('Location: index.php?controller=cliente&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al crear el cliente: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=cliente&action=create');
            exit;
        }
    }

    // Mostrar formulario para editar cliente
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            $_SESSION['error'] = 'Cliente no encontrado';
            header('Location: index.php?controller=cliente&action=index');
            exit;
        }
        
        require_once 'views/clientes/form.php';
    }

    // Actualizar cliente
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'tipo' => $_POST['tipo'] ?? 'particular'
            ];

            $errores = $this->validar($data, $id);

            if (empty($errores)) {
                try {
                    $this->clienteModel->update($id, $data);
                    $_SESSION['success'] = 'Cliente actualizado exitosamente';
                    header('Location: index.php?controller=cliente&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al actualizar el cliente: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=cliente&action=edit&id=' . $id);
            exit;
        }
    }

    // Eliminar cliente
    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        // Verificar si tiene órdenes activas
        if ($this->clienteModel->tieneOrdenesActivas($id)) {
            $_SESSION['error'] = 'No se puede eliminar el cliente porque tiene órdenes activas';
            header('Location: index.php?controller=cliente&action=index');
            exit;
        }
        
        try {
            $this->clienteModel->delete($id);
            $_SESSION['success'] = 'Cliente eliminado exitosamente';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar el cliente: ' . $e->getMessage();
        }
        
        header('Location: index.php?controller=cliente&action=index');
        exit;
    }

    // Buscar clientes (AJAX)
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $clientes = $this->clienteModel->buscar($termino);
        header('Content-Type: application/json');
        echo json_encode($clientes);
        exit;
    }

    // Validar datos del cliente
    private function validar($data, $id = null) {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($data['nombre']) < 3) {
            $errores['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (empty($data['email'])) {
            $errores['email'] = 'El email es obligatorio';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El email no es válido';
        } elseif ($this->clienteModel->emailExiste($data['email'], $id)) {
            $errores['email'] = 'El email ya está registrado';
        }

        if (empty($data['telefono'])) {
            $errores['telefono'] = 'El teléfono es obligatorio';
        }

        return $errores;
    }

    // Ver detalles del cliente
    public function show() {
        $id = $_GET['id'] ?? 0;
        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            $_SESSION['error'] = 'Cliente no encontrado';
            header('Location: index.php?controller=cliente&action=index');
            exit;
        }
        
        $equipos = $this->clienteModel->getEquiposCliente($id);
        require_once 'views/clientes/show.php';
    }
}
?>