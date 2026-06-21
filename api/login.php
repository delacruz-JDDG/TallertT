<?php
/**
 * api/login.php
 * Endpoint para autenticación de usuarios
 */

// ============================================
// 1. CONFIGURACIÓN
// ============================================

require_once 'config.php';
require_once '../models/Usuario.php';

$usuarioModel = new Usuario();

// ============================================
// 2. OBTENER DATOS DEL FORMULARIO
// ============================================

$data = obtenerDatos();
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// ============================================
// 3. VALIDAR CAMPOS
// ============================================

if (empty($username) || empty($password)) {
    responder(['success' => false, 'message' => 'Usuario y contraseña son obligatorios'], 400);
}

// ============================================
// 4. AUTENTICAR USUARIO
// ============================================

$usuario = $usuarioModel->authenticate($username, $password);

if ($usuario) {
    // Login exitoso
    responder([
        'success' => true,
        'message' => 'Login exitoso',
        'usuario' => [
            'id' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'username' => $usuario['username'],
            'rol' => $usuario['rol']
        ]
    ]);
} else {
    // Login fallido
    responder(['success' => false, 'message' => 'Usuario o contraseña incorrectos'], 401);
}
?>