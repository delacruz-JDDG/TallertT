<?php
/**
 * api/ordenes.php
 * Endpoint para la gestión de Órdenes de Servicio
 * Proporciona operaciones CRUD, gestión de repuestos y reportes
 */

// ============================================
// 1. CONFIGURACIÓN Y MODELOS
// ============================================

require_once 'config.php';
require_once '../models/Orden.php';
require_once '../models/Equipo.php';
require_once '../models/Tecnico.php';
require_once '../models/Repuesto.php';
require_once '../models/Cliente.php';

// Instancias de todos los modelos necesarios
$ordenModel = new Orden();
$equipoModel = new Equipo();
$tecnicoModel = new Tecnico();
$repuestoModel = new Repuesto();
$clienteModel = new Cliente();

// Obtiene la acción desde la URL (ej: ?action=listar)
$action = $_GET['action'] ?? '';

// ============================================
// 2. ENRUTADOR
// ============================================

switch ($action) {
    
    // ------------------------------------------
    // LISTAR ÓRDENES
    // ------------------------------------------
    case 'listar':
        // Obtiene todas las órdenes con datos relacionados (cliente, equipo, técnico)
        $ordenes = $ordenModel->getOrdenesCompletas();
        responder($ordenes);
        break;

    // ------------------------------------------
    // OBTENER UNA ORDEN POR ID (con sus repuestos)
    // ------------------------------------------
    case 'obtener':
        $id = $_GET['id'] ?? 0;
        // Obtiene los datos completos de la orden
        $orden = $ordenModel->getOrdenCompleta($id);
        
        if ($orden) {
            // Agrega los repuestos asociados a la orden
            $orden['repuestos'] = $ordenModel->getRepuestosOrden($id);
            responder($orden);
        } else {
            responder(['error' => 'Orden no encontrada'], 404);
        }
        break;

    // ------------------------------------------
    // CREAR NUEVA ORDEN (POST)
    // ------------------------------------------
    case 'guardar':
        $data = obtenerDatos();
        
        // Validación: equipo, técnico y síntoma son obligatorios
        if (empty($data['id_equipo']) || empty($data['id_tecnico']) || empty($data['sintoma'])) {
            responder(['error' => 'Faltan datos obligatorios'], 400);
        }
        
        // Valores por defecto
        $data['estado'] = 'en_diagnostico';    // Estado inicial
        $data['mano_obra'] = $data['mano_obra'] ?? 0;
        $data['total'] = 0;
        
        // Verifica que el equipo no tenga otra orden activa
        if ($ordenModel->equipoTieneOrdenActiva($data['id_equipo'])) {
            responder(['error' => 'El equipo ya tiene una orden activa'], 400);
        }
        
        // Inserta la orden en la base de datos
        $id = $ordenModel->insert($data);
        
        // Recalcula el total (mano de obra + repuestos)
        $ordenModel->recalcularTotal($id);
        
        responder(['success' => true, 'id' => $id, 'message' => 'Orden creada']);
        break;

    // ------------------------------------------
    // ACTUALIZAR ORDEN (PUT)
    // ------------------------------------------
    case 'actualizar':
        $data = obtenerDatos();
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        // Obtiene los datos actuales de la orden
        $orden_actual = $ordenModel->getById($id);
        
        // Si se cambió el equipo, verifica que no tenga otra orden activa
        if ($data['id_equipo'] != $orden_actual['id_equipo']) {
            if ($ordenModel->equipoTieneOrdenActiva($data['id_equipo'], $id)) {
                responder(['error' => 'El equipo ya tiene otra orden activa'], 400);
            }
        }
        
        // Datos a actualizar
        $update_data = [
            'id_equipo' => $data['id_equipo'],
            'id_tecnico' => $data['id_tecnico'],
            'sintoma' => $data['sintoma'],
            'mano_obra' => $data['mano_obra'] ?? 0,
            'estado' => $data['estado'] ?? 'en_diagnostico'
        ];
        
        // Actualiza la orden
        $ordenModel->update($id, $update_data);
        
        // Recalcula el total
        $ordenModel->recalcularTotal($id);
        
        responder(['success' => true, 'message' => 'Orden actualizada']);
        break;

    // ------------------------------------------
    // ELIMINAR ORDEN (DELETE)
    // ------------------------------------------
    case 'eliminar':
        $id = $_GET['id'] ?? 0;
        
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        // Verifica que la orden no esté entregada
        $orden = $ordenModel->getById($id);
        if ($orden && $orden['estado'] == 'entregado') {
            responder(['error' => 'No se puede eliminar una orden entregada'], 400);
        }
        
        // Devuelve los repuestos al stock
        $repuestos = $ordenModel->getRepuestosOrden($id);
        foreach ($repuestos as $repuesto) {
            $repuestoModel->actualizarStock($repuesto['id_repuesto'], $repuesto['cantidad'], 'sumar');
        }
        
        // Elimina la orden
        $ordenModel->delete($id);
        
        responder(['success' => true, 'message' => 'Orden eliminada']);
        break;

    // ------------------------------------------
    // AGREGAR REPUESTO A UNA ORDEN
    // ------------------------------------------
    case 'agregar_repuesto':
        $data = obtenerDatos();
        $id_orden = $_GET['id'] ?? 0;
        $id_repuesto = $data['id_repuesto'] ?? 0;
        $cantidad = $data['cantidad'] ?? 1;
        
        // Validación
        if ($id_orden <= 0 || $id_repuesto <= 0 || $cantidad <= 0) {
            responder(['error' => 'Datos inválidos'], 400);
        }
        
        try {
            // Agrega el repuesto a la orden (valida stock automáticamente)
            $ordenModel->agregarRepuesto($id_orden, $id_repuesto, $cantidad);
            responder(['success' => true, 'message' => 'Repuesto agregado']);
        } catch (Exception $e) {
            responder(['error' => $e->getMessage()], 400);
        }
        break;

    // ------------------------------------------
    // ELIMINAR REPUESTO DE UNA ORDEN
    // ------------------------------------------
    case 'eliminar_repuesto':
        $id_orden = $_GET['id'] ?? 0;
        $id_repuesto = $_GET['id_repuesto'] ?? 0;
        
        if ($id_orden <= 0 || $id_repuesto <= 0) {
            responder(['error' => 'Datos inválidos'], 400);
        }
        
        try {
            // Elimina el repuesto de la orden y devuelve al stock
            $ordenModel->eliminarRepuesto($id_orden, $id_repuesto);
            responder(['success' => true, 'message' => 'Repuesto eliminado']);
        } catch (Exception $e) {
            responder(['error' => $e->getMessage()], 400);
        }
        break;

    // ------------------------------------------
    // CAMBIAR ESTADO DE UNA ORDEN
    // ------------------------------------------
    case 'cambiar_estado':
        $data = obtenerDatos();
        $id = $_GET['id'] ?? 0;
        $estado = $data['estado'] ?? '';
        
        // Lista de estados válidos
        $estados_validos = ['en_diagnostico', 'en_espera_repuestos', 'en_reparacion', 'pendiente', 'entregado'];
        
        if ($id <= 0 || !in_array($estado, $estados_validos)) {
            responder(['error' => 'Datos inválidos'], 400);
        }
        
        try {
            // Cambia el estado de la orden
            $ordenModel->cambiarEstado($id, $estado);
            responder(['success' => true, 'message' => 'Estado actualizado']);
        } catch (Exception $e) {
            responder(['error' => $e->getMessage()], 400);
        }
        break;

    // ------------------------------------------
    // REPORTE DE INGRESOS POR TÉCNICO
    // ------------------------------------------
    case 'reporte_tecnico':
        $id_tecnico = $_GET['id_tecnico'] ?? 0;
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        if ($id_tecnico <= 0) {
            responder(['error' => 'Técnico inválido'], 400);
        }
        
        // Obtiene las órdenes del técnico en el período
        $reporte = $ordenModel->reporteIngresosTecnico($id_tecnico, $fecha_inicio, $fecha_fin);
        
        // Calcula el total de ingresos
        $total = $ordenModel->totalIngresosTecnico($id_tecnico, $fecha_inicio, $fecha_fin);
        
        responder([
            'ordenes' => $reporte,
            'total_ingresos' => $total,
            'total_ordenes' => count($reporte)
        ]);
        break;

    // ------------------------------------------
    // ACCIÓN NO VÁLIDA
    // ------------------------------------------
    default:
        responder(['error' => 'Acción no válida'], 400);
        break;
}
?>