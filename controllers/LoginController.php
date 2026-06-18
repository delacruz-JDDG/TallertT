<?php
// controllers/LoginController.php

class LoginController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    // Mostrar vista de login
    public function index() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }
        require_once 'views/login.php';
    }

    // Procesar el login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validar que los campos no estén vacíos
            if (empty($username) || empty($password)) {
                $_SESSION['login_error'] = 'Por favor ingrese usuario y contraseña';
                header('Location: index.php?controller=login&action=index');
                exit;
            }

            // Autenticar usuario
            $usuario = $this->usuarioModel->authenticate($username, $password);

            if ($usuario) {
                // Guardar datos en sesión
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                $_SESSION['usuario_username'] = $usuario['username'];

                // Redirigir al dashboard
                header('Location: index.php?controller=dashboard&action=index');
                exit;
            } else {
                $_SESSION['login_error'] = 'Usuario o contraseña incorrectos';
                header('Location: index.php?controller=login&action=index');
                exit;
            }
        }
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header('Location: index.php?controller=login&action=index');
        exit;
    }
}
?>