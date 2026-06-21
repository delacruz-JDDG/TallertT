<?php
/**
 * api/config.php
 * Archivo de configuración para la API del sistema
 * Define cabeceras, respuestas JSON y funciones auxiliares
 */

// ============================================
// 1. CABECERAS CORS PARA PERMITIR PETICIONES DE REACT
// ============================================

// Permitir solicitudes desde cualquier origen (React corre en otro puerto)
header('Access-Control-Allow-Origin: *');

// Permitir estos métodos HTTP
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Permitir estos encabezados en las peticiones
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Si es una solicitud OPTIONS (preflight), responder con éxito
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Indica que la respuesta será en formato JSON
header('Content-Type: application/json');

// ============================================
// 1. CABECERAS HTTP
// ============================================

// Indica que la respuesta será en formato JSON
header('Content-Type: application/json');

// Permite que cualquier origen (dominio) pueda acceder a la API (CORS)
header('Access-Control-Allow-Origin: *');

// Define los métodos HTTP permitidos
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

// Define los encabezados permitidos en las peticiones
header('Access-Control-Allow-Headers: Content-Type');

// ============================================
// 2. INCLUIR CONFIGURACIÓN DE BASE DE DATOS
// ============================================

// Carga el archivo de conexión a la base de datos
require_once '../config/database.php';

// ============================================
// 3. FUNCIONES AUXILIARES
// ============================================

/**
 * Función para enviar respuestas en formato JSON
 * 
 * @param mixed $data   - Datos a enviar (array, objeto, etc.)
 * @param int   $status - Código HTTP (200, 400, 404, 500, etc.)
 */
function responder($data, $status = 200) {
    http_response_code($status);          // Establece el código HTTP
    echo json_encode($data);              // Convierte los datos a JSON y los imprime
    exit;                                 // Detiene la ejecución del script
}

/**
 * Función para obtener datos del cuerpo de la petición
 * Útil para POST, PUT, DELETE donde los datos vienen en el body
 * 
 * @return array Datos decodificados del JSON recibido
 */
function obtenerDatos() {
    // Lee el contenido bruto de la petición (php://input)
    // Lo decodifica de JSON a un array asociativo
    return json_decode(file_get_contents('php://input'), true);
}
?>