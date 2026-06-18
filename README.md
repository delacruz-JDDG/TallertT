# TallerT - Sistema de Gestión de Órdenes de Servicio

## 📋 Descripción
Sistema web para la gestión de órdenes de servicio de un taller de reparación de equipos electrónicos. Permite registrar clientes, técnicos, equipos, repuestos y gestionar el ciclo completo de las órdenes de servicio.

## 🚀 Características
- Autenticación de usuarios
- Gestión de clientes (CRUD)
- Gestión de técnicos (CRUD)
- Gestión de equipos (CRUD)
- Gestión de repuestos (CRUD)
- Gestión de órdenes de servicio
- Asignación de técnicos a órdenes
- Control de stock de repuestos
- Cálculo automático de totales
- Reporte de ingresos por técnico
- Dashboard con estadísticas

## 🛠️ Tecnologías utilizadas
- PHP (Arquitectura MVC)
- MySQL
- HTML5
- CSS3 (Bootstrap 5)
- JavaScript (AJAX, Chart.js)
- Font Awesome

## 📁 Estructura del proyecto

tallert/
├── config/
│ └── database.php
├── controllers/
│ ├── LoginController.php
│ ├── DashboardController.php
│ ├── ClienteController.php
│ ├── TecnicoController.php
│ ├── EquipoController.php
│ ├── RepuestoController.php
│ └── OrdenController.php
├── models/
│ ├── Model.php
│ ├── Usuario.php
│ ├── DashboardModel.php
│ ├── Cliente.php
│ ├── Tecnico.php
│ ├── Equipo.php
│ ├── Repuesto.php
│ └── Orden.php
├── views/
│ ├── login.php
│ ├── dashboard/
│ ├── clientes/
│ ├── tecnicos/
│ ├── equipos/
│ ├── repuestos/
│ ├── ordenes/
│ └── partials/
├── assets/
│ ├── css/
│ └── js/
├── index.php
└── README.md