<?php
/**
 * api/clientes.php
 * Endpoint para la gestión de Clientes
 * Proporciona operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 */

// ============================================
// 1. CONFIGURACIÓN Y MODELO
// ============================================

// Carga la configuración de la API (cabeceras, funciones, base de datos)
require_once 'config.php';

// Carga el modelo de Clientes para interactuar con la base de datos
require_once '../models/Cliente.php';

// Instancia del modelo Cliente
$clienteModel = new Cliente();

// Obtiene la acción enviada por la URL (ej: ?action=listar)
$action = $_GET['action'] ?? '';

// ============================================
// 2. ENRUTADOR (Router)
// ============================================

// Según la acción recibida, ejecuta la operación correspondiente
switch ($action) {
    
    // ------------------------------------------
    // CASO 1: LISTAR CLIENTES
    // ------------------------------------------
    case 'listar':
        // Obtiene todos los clientes con su conteo de equipos
        $clientes = $clienteModel->getClientesConEquipos();
        // Devuelve la lista en JSON
        responder($clientes);
        break;

    // ------------------------------------------
    // CASO 2: OBTENER UN CLIENTE POR ID
    // ------------------------------------------
    case 'obtener':
        // Obtiene el ID desde la URL (ej: ?id=5)
        $id = $_GET['id'] ?? 0;
        // Busca el cliente en la base de datos
        $cliente = $clienteModel->getById($id);
        
        // Si el cliente existe, lo devuelve
        if ($cliente) {
            responder($cliente);
        } else {
            // Si no existe, devuelve error 404
            responder(['error' => 'Cliente no encontrado'], 404);
        }
        break;

    // ------------------------------------------
    // CASO 3: GUARDAR NUEVO CLIENTE (POST)
    // ------------------------------------------
    case 'guardar':
        // Obtiene los datos enviados en el cuerpo de la petición
        $data = obtenerDatos();
        
        // Validación: campos obligatorios
        if (empty($data['nombre']) || empty($data['email']) || empty($data['telefono'])) {
            // Si faltan datos, devuelve error 400
            responder(['error' => 'Faltan datos obligatorios'], 400);
        }
        
        // Inserta el nuevo cliente en la base de datos
        $id = $clienteModel->insert($data);
        
        // Devuelve respuesta de éxito con el ID del cliente creado
        responder(['success' => true, 'id' => $id, 'message' => 'Cliente creado']);
        break;

    // ------------------------------------------
    // CASO 4: ACTUALIZAR CLIENTE (PUT)
    // ------------------------------------------
    case 'actualizar':
        // Obtiene los datos enviados en el cuerpo
        $data = obtenerDatos();
        
        // Obtiene el ID desde la URL
        $id = $_GET['id'] ?? 0;
        
        // Validación: ID debe ser mayor a 0
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        // Actualiza el cliente en la base de datos
        $clienteModel->update($id, $data);
        
        // Devuelve respuesta de éxito
        responder(['success' => true, 'message' => 'Cliente actualizado']);
        break;

    // ------------------------------------------
    // CASO 5: ELIMINAR CLIENTE (DELETE)
    // ------------------------------------------
    case 'eliminar':
        // Obtiene el ID desde la URL
        $id = $_GET['id'] ?? 0;
        
        // Validación: ID válido
        if ($id <= 0) {
            responder(['error' => 'ID inválido'], 400);
        }
        
        // Elimina el cliente de la base de datos
        $clienteModel->delete($id);
        
        // Devuelve respuesta de éxito
        responder(['success' => true, 'message' => 'Cliente eliminado']);
        break;

    // ------------------------------------------
    // CASO POR DEFECTO: ACCIÓN NO VÁLIDA
    // ------------------------------------------
    default:
        // Si la acción no está definida, devuelve error
        responder(['error' => 'Acción no válida'], 400);
        break;
}
?>