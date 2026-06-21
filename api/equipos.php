<?php
/**
 * api/equipos.php
 * Endpoint para la gestión de Equipos
 * Proporciona operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 */

// ============================================
// 1. CONFIGURACIÓN Y MODELO
// ============================================

require_once 'config.php';
require_once '../models/Equipo.php';

$equipoModel = new Equipo();
$action = $_GET['action'] ?? '';

// ============================================
// 2. ENRUTADOR
// ============================================

switch ($action) {
    
    // ------------------------------------------
    // LISTAR EQUIPOS
    // ------------------------------------------
    case 'listar':
        // Obtiene todos los equipos con datos del cliente
        $equipos = $equipoModel->getEquiposCompletos();
        responder($equipos);
        break;

    // ------------------------------------------
    // OBTENER EQUIPO POR ID
    // ------------------------------------------
    case 'obtener':
        $id = $_GET['id'] ?? 0;
        $equipo = $equipoModel->getById($id);
        
        if ($equipo) {
            responder($equipo);
        } else {
            responder(['error' => 'Equipo no encontrado'], 404);
        }
        break;

    // ------------------------------------------
    // GUARDAR NUEVO EQUIPO (POST)
    // ------------------------------------------
    case 'guardar':
        $data = obtenerDatos();
        
        // Validación: cliente, marca, modelo y serial son obligatorios
        if (empty($data['id_cliente']) || empty($data['marca']) || empty($data['modelo']) || empty($data['serial'])) {
            responder(['error' => 'Faltan datos obligatorios'], 400);
        }
        
        $id = $equipoModel->insert($data);
        responder(['success' => true, 'id' => $id, 'message' => 'Equipo creado']);
        break;

    // ------------------------------------------
    // ACTUALIZAR EQUIPO (PUT)
    // ------------------------------------------
    case 'actualizar':
        $data = obtenerDatos();
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        $equipoModel->update($id, $data);
        responder(['success' => true, 'message' => 'Equipo actualizado']);
        break;

    // ------------------------------------------
    // ELIMINAR EQUIPO (DELETE)
    // ------------------------------------------
    case 'eliminar':
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        $equipoModel->delete($id);
        responder(['success' => true, 'message' => 'Equipo eliminado']);
        break;

    // ------------------------------------------
    // ACCIÓN NO VÁLIDA
    // ------------------------------------------
    default:
        responder(['error' => 'Acción no válida'], 400);
        break;
}
?>