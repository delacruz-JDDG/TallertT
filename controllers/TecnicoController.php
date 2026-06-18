<?php
// controllers/TecnicoController.php

require_once 'models/Tecnico.php';

class TecnicoController {
    private $tecnicoModel;

    public function __construct() {
        $this->tecnicoModel = new Tecnico();
    }

    // Listar técnicos
    public function index() {
        $tecnicos = $this->tecnicoModel->getTecnicosConOrdenes();
        require_once 'views/tecnicos/index.php';
    }

    // Mostrar formulario para nuevo técnico
    public function create() {
        require_once 'views/tecnicos/form.php';
    }

    // Guardar nuevo técnico
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'especialidad' => trim($_POST['especialidad'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'estado' => $_POST['estado'] ?? 'activo'
            ];

            $errores = $this->validar($data);

            if (empty($errores)) {
                try {
                    $id = $this->tecnicoModel->insert($data);
                    $_SESSION['success'] = 'Técnico creado exitosamente';
                    header('Location: index.php?controller=tecnico&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al crear el técnico: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=tecnico&action=create');
            exit;
        }
    }

    // Mostrar formulario para editar técnico
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $tecnico = $this->tecnicoModel->getById($id);
        
        if (!$tecnico) {
            $_SESSION['error'] = 'Técnico no encontrado';
            header('Location: index.php?controller=tecnico&action=index');
            exit;
        }
        
        require_once 'views/tecnicos/form.php';
    }

    // Actualizar técnico
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            
            $data = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'especialidad' => trim($_POST['especialidad'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'estado' => $_POST['estado'] ?? 'activo'
            ];

            $errores = $this->validar($data, $id);

            if (empty($errores)) {
                try {
                    $this->tecnicoModel->update($id, $data);
                    $_SESSION['success'] = 'Técnico actualizado exitosamente';
                    header('Location: index.php?controller=tecnico&action=index');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al actualizar el técnico: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['old'] = $data;
            }
            
            header('Location: index.php?controller=tecnico&action=edit&id=' . $id);
            exit;
        }
    }

    // Eliminar técnico
    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        // Verificar si tiene órdenes asignadas
        if ($this->tecnicoModel->tieneOrdenes($id)) {
            $_SESSION['error'] = 'No se puede eliminar el técnico porque tiene órdenes asignadas';
            header('Location: index.php?controller=tecnico&action=index');
            exit;
        }
        
        try {
            $this->tecnicoModel->delete($id);
            $_SESSION['success'] = 'Técnico eliminado exitosamente';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al eliminar el técnico: ' . $e->getMessage();
        }
        
        header('Location: index.php?controller=tecnico&action=index');
        exit;
    }

    // Ver detalle del técnico
    public function show() {
        $id = $_GET['id'] ?? 0;
        $tecnico = $this->tecnicoModel->getById($id);
        
        if (!$tecnico) {
            $_SESSION['error'] = 'Técnico no encontrado';
            header('Location: index.php?controller=tecnico&action=index');
            exit;
        }
        
        $ordenes = $this->tecnicoModel->getOrdenesTecnico($id);
        require_once 'views/tecnicos/show.php';
    }

    // Cambiar estado (activar/inactivar) vía AJAX
    public function toggleEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $estado = $_POST['estado'] ?? 'activo';
            
            // Validar que el estado sea válido
            if (!in_array($estado, ['activo', 'inactivo'])) {
                echo json_encode(['success' => false, 'message' => 'Estado no válido']);
                exit;
            }
            
            try {
                $this->tecnicoModel->cambiarEstado($id, $estado);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    // Buscar técnicos (AJAX)
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $tecnicos = $this->tecnicoModel->buscar($termino);
        header('Content-Type: application/json');
        echo json_encode($tecnicos);
        exit;
    }

    // Obtener técnicos activos (para combos - AJAX)
    public function getActivos() {
        $tecnicos = $this->tecnicoModel->getActivos();
        header('Content-Type: application/json');
        echo json_encode($tecnicos);
        exit;
    }

    // Validar datos del técnico
    private function validar($data, $id = null) {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($data['nombre']) < 3) {
            $errores['nombre'] = 'El nombre debe tener al menos 3 caracteres';
        }

        if (empty($data['especialidad'])) {
            $errores['especialidad'] = 'La especialidad es obligatoria';
        }

        if (empty($data['telefono'])) {
            $errores['telefono'] = 'El teléfono es obligatorio';
        }

        return $errores;
    }
}
?>