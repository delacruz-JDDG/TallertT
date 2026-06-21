<?php
// api/config.php


header('Access-Control-Allow-Headers: Content-Type, Authorization');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


header('Content-Type: application/json');

// ============================================
// 2. BASE DE DATOS
// ============================================

require_once __DIR__ . '/../config/database.php';

// ============================================
// 3. FUNCIONES


function responder($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}


function obtenerDatos() {
  
    return json_decode(file_get_contents('php://input'), true);
}
?>