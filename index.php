<?php
// index.php - Controlador Frontal

session_start();

// Incluir configuración de base de datos
require_once 'config/database.php';

// Función para cargar automáticamente los controladores y modelos
function autoload($className) {
    $paths = [
        'controllers/',
        'models/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}

spl_autoload_register('autoload');

// Obtener el controlador y acción desde la URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Verificar si el usuario está logueado (excepto para login)
if ($controller !== 'login' && !isset($_SESSION['usuario_id'])) {
    header('Location: index.php?controller=login&action=index');
    exit;
}

// Construir el nombre del controlador
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = 'controllers/' . $controllerName . '.php';

// Verificar si el controlador existe
if (!file_exists($controllerFile)) {
    die("Controlador no encontrado: $controllerName en $controllerFile");
}

require_once $controllerFile;

// Verificar si la clase existe
if (!class_exists($controllerName)) {
    die("Clase no encontrada: $controllerName");
}

$controllerObj = new $controllerName();

// Verificar si el método existe
if (!method_exists($controllerObj, $action)) {
    die("Acción no encontrada: $action en $controllerName");
}

// Ejecutar
$controllerObj->$action();
?>