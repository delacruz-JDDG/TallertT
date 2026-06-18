<?php
// controllers/DashboardController.php

require_once 'models/DashboardModel.php';
require_once 'models/Usuario.php';

class DashboardController {
    private $dashboardModel;
    private $usuarioModel;

    public function __construct() {
        $this->dashboardModel = new DashboardModel();
        $this->usuarioModel = new Usuario();
    }

    public function index() {
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?controller=login&action=index');
            exit;
        }

        // Obtener datos para el dashboard
        $data = [
            // Tarjetas de estadísticas
            'ordenes_activas' => $this->dashboardModel->getOrdenesActivas(),
            'total_clientes' => $this->dashboardModel->getTotalClientes(),
            'tecnicos_activos' => $this->dashboardModel->getTecnicosActivos(),
            'stock_repuestos' => $this->dashboardModel->getStockRepuestos(),
            
            // Porcentajes de cambio (simulados con datos de la semana pasada)
            'cambio_ordenes' => '+12',
            'cambio_clientes' => '+8',
            'cambio_tecnicos' => '+5',
            'cambio_repuestos' => '+2',
            
            // Órdenes recientes
            'ordenes_recientes' => $this->dashboardModel->getOrdenesRecientes(5),
            
            // Resumen de órdenes
            'ordenes_7dias' => $this->dashboardModel->getOrdenesUltimos7Dias(),
            'total_ordenes' => $this->dashboardModel->getTotalOrdenes(),
            
            // Datos para gráfico
            'datos_grafico' => $this->dashboardModel->getDatosGrafico(),
            
            // Actividad reciente
            'actividad_reciente' => $this->dashboardModel->getActividadReciente(),
            
            // Información del usuario
            'usuario' => $this->usuarioModel->getById($_SESSION['usuario_id'])
        ];

        // Cargar la vista
        require_once 'views/dashboard/index.php';
    }
}
?>