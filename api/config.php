<?php
// api/config.php

// ============================================
// CABECERAS CORS
// ============================================

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();

    }


    header('Content-Type: application/json');

// ============================================
// BASE DE DATOS
// ============================================

require_once __DIR__ . '/../config/database.php';

// ============================================
// FUNCIONES AUXILIARES
// ============================================

function responder($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}


function obtenerDatos() {
  
return json_decode(file_get_contents('php://input'), true);
}
?>