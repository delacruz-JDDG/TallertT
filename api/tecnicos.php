<?php
/**
 * api/tecnicos.php
 * Endpoint para la gestión de Técnicos
 * Proporciona operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 */

// ============================================
// 1. CONFIGURACIÓN Y MODELO
// ============================================

require_once 'config.php';
require_once '../models/Tecnico.php';

$tecnicoModel = new Tecnico();
$action = $_GET['action'] ?? '';

// ============================================
// 2. ENRUTADOR
// ============================================

switch ($action) {
    
    // ------------------------------------------
    // LISTAR TÉCNICOS
    // ------------------------------------------
    case 'listar':
        // Obtiene todos los técnicos con su conteo de órdenes
        $tecnicos = $tecnicoModel->getTecnicosConOrdenes();
        responder($tecnicos);
        break;

    // ------------------------------------------
    // OBTENER TÉCNICO POR ID
    // ------------------------------------------
    case 'obtener':
        $id = $_GET['id'] ?? 0;
        $tecnico = $tecnicoModel->getById($id);
        
        if ($tecnico) {
            responder($tecnico);
        } else {
            responder(['error' => 'Técnico no encontrado'], 404);
        }
        break;

    // ------------------------------------------
    // GUARDAR NUEVO TÉCNICO (POST)
    // ------------------------------------------
    case 'guardar':
        $data = obtenerDatos();
        
        // Validación: nombre, especialidad y teléfono son obligatorios
        if (empty($data['nombre']) || empty($data['especialidad']) || empty($data['telefono'])) {
            responder(['error' => 'Faltan datos obligatorios'], 400);
        }
        
        $id = $tecnicoModel->insert($data);
        responder(['success' => true, 'id' => $id, 'message' => 'Técnico creado']);
        break;

    // ------------------------------------------
    // ACTUALIZAR TÉCNICO (PUT)
    // ------------------------------------------
    case 'actualizar':
        $data = obtenerDatos();
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        $tecnicoModel->update($id, $data);
        responder(['success' => true, 'message' => 'Técnico actualizado']);
        break;

    // ------------------------------------------
    // ELIMINAR TÉCNICO (DELETE)
    // ------------------------------------------
    case 'eliminar':
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        $tecnicoModel->delete($id);
        responder(['success' => true, 'message' => 'Técnico eliminado']);
        break;

    // ------------------------------------------
    // ACCIÓN NO VÁLIDA
    // ------------------------------------------
    default:
        responder(['error' => 'Acción no válida'], 400);
        break;
}
?>