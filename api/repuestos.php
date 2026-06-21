<?php
/**
 * api/repuestos.php
 * Endpoint para la gestión de Repuestos
 * Proporciona operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 */

// ============================================
// 1. CONFIGURACIÓN Y MODELO
// ============================================

require_once 'config.php';
require_once '../models/Repuesto.php';

$repuestoModel = new Repuesto();
$action = $_GET['action'] ?? '';

// ============================================
// 2. ENRUTADOR
// ============================================

switch ($action) {
    
    // ------------------------------------------
    // LISTAR REPUESTOS
    // ------------------------------------------
    case 'listar':
        // Obtiene todos los repuestos con estadísticas de uso
        $repuestos = $repuestoModel->getRepuestosConUso();
        responder($repuestos);
        break;

    // ------------------------------------------
    // OBTENER REPUESTO POR ID
    // ------------------------------------------
    case 'obtener':
        $id = $_GET['id'] ?? 0;
        $repuesto = $repuestoModel->getById($id);
        
        if ($repuesto) {
            responder($repuesto);
        } else {
            responder(['error' => 'Repuesto no encontrado'], 404);
        }
        break;

    // ------------------------------------------
    // GUARDAR NUEVO REPUESTO (POST)
    // ------------------------------------------
    case 'guardar':
        $data = obtenerDatos();
        
        // Validación: nombre, precio y stock válido
        if (empty($data['nombre']) || empty($data['precio_unitario']) || $data['stock'] < 0) {
            responder(['error' => 'Faltan datos obligatorios o stock inválido'], 400);
        }
        
        $id = $repuestoModel->insert($data);
        responder(['success' => true, 'id' => $id, 'message' => 'Repuesto creado']);
        break;

    // ------------------------------------------
    // ACTUALIZAR REPUESTO (PUT)
    // ------------------------------------------
    case 'actualizar':
        $data = obtenerDatos();
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        $repuestoModel->update($id, $data);
        responder(['success' => true, 'message' => 'Repuesto actualizado']);
        break;

    // ------------------------------------------
    // ELIMINAR REPUESTO (DELETE)
    // ------------------------------------------
    case 'eliminar':
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        $repuestoModel->delete($id);
        responder(['success' => true, 'message' => 'Repuesto eliminado']);
        break;

    // ------------------------------------------
    // ACCIÓN NO VÁLIDA
    // ------------------------------------------
    default:
        responder(['error' => 'Acción no válida'], 400);
        break;
}
?>