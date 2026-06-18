<?php
// models/DashboardModel.php

require_once 'Model.php';

class DashboardModel extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    // Obtener total de órdenes activas (no entregadas)
    public function getOrdenesActivas() {
        $sql = "SELECT COUNT(*) as total FROM ordenes_servicio 
                WHERE estado != 'entregado'";
        $result = $this->db->getOne($sql);
        return $result['total'] ?? 0;
    }

    // Obtener total de clientes
    public function getTotalClientes() {
        return $this->db->count('clientes');
    }

    // Obtener total de técnicos activos
    public function getTecnicosActivos() {
        $sql = "SELECT COUNT(*) as total FROM tecnicos WHERE estado = 'activo'";
        $result = $this->db->getOne($sql);
        return $result['total'] ?? 0;
    }

    // Obtener stock total de repuestos
    public function getStockRepuestos() {
        $sql = "SELECT SUM(stock) as total FROM repuestos";
        $result = $this->db->getOne($sql);
        return $result['total'] ?? 0;
    }

    // Obtener órdenes recientes con datos del cliente, equipo y técnico
    public function getOrdenesRecientes($limit = 5) {
        $sql = "SELECT 
                    o.id_orden,
                    o.id_equipo,
                    o.id_tecnico,
                    o.fecha_recepcion,
                    o.sintoma,
                    o.estado,
                    o.mano_obra,
                    o.total,
                    o.fecha_entrega,
                    c.nombre as cliente_nombre,
                    c.email as cliente_email,
                    c.telefono as cliente_telefono,
                    e.marca,
                    e.modelo,
                    e.serial,
                    e.tipo as equipo_tipo,
                    t.nombre as tecnico_nombre,
                    t.especialidad
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                ORDER BY o.fecha_recepcion DESC
                LIMIT $limit";
        return $this->db->getAll($sql);
    }

    // Obtener resumen de órdenes por estado
    public function getResumenOrdenes() {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as cantidad
                FROM ordenes_servicio
                GROUP BY estado";
        return $this->db->getAll($sql);
    }

    // Obtener total de órdenes de los últimos 7 días
    public function getOrdenesUltimos7Dias() {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio 
                WHERE fecha_recepcion >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $result = $this->db->getOne($sql);
        return $result['total'] ?? 0;
    }

    // Obtener total de órdenes
    public function getTotalOrdenes() {
        return $this->db->count('ordenes_servicio');
    }

    // Obtener actividad reciente (CORREGIDO)
    public function getActividadReciente() {
        // Consulta 1: Nuevas órdenes
        $sql_nuevas = "SELECT 
                            'Nueva orden creada' as evento,
                            CONCAT(c.nombre, ' - ', e.marca, ' ', e.modelo) as descripcion,
                            o.fecha_recepcion as fecha,
                            CASE 
                                WHEN TIMESTAMPDIFF(MINUTE, o.fecha_recepcion, NOW()) < 60 
                                THEN CONCAT(TIMESTAMPDIFF(MINUTE, o.fecha_recepcion, NOW()), ' min')
                                WHEN TIMESTAMPDIFF(HOUR, o.fecha_recepcion, NOW()) < 24 
                                THEN CONCAT(TIMESTAMPDIFF(HOUR, o.fecha_recepcion, NOW()), ' h')
                                ELSE CONCAT(TIMESTAMPDIFF(DAY, o.fecha_recepcion, NOW()), ' d')
                            END as tiempo
                        FROM ordenes_servicio o
                        INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                        INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                        WHERE o.fecha_recepcion >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                        ORDER BY o.fecha_recepcion DESC
                        LIMIT 4";
        
        // Consulta 2: Cambios de estado
        $sql_estados = "SELECT 
                            'Estado actualizado' as evento,
                            CONCAT('Orden #', o.id_orden, ' - ', 
                                CASE o.estado
                                    WHEN 'en_diagnostico' THEN 'En diagnóstico'
                                    WHEN 'en_espera_repuestos' THEN 'En espera de repuestos'
                                    WHEN 'en_reparacion' THEN 'En reparación'
                                    WHEN 'pendiente' THEN 'Pendiente'
                                    WHEN 'entregado' THEN 'Entregado'
                                END) as descripcion,
                            o.fecha_recepcion as fecha,
                            CASE 
                                WHEN TIMESTAMPDIFF(MINUTE, o.fecha_recepcion, NOW()) < 60 
                                THEN CONCAT(TIMESTAMPDIFF(MINUTE, o.fecha_recepcion, NOW()), ' min')
                                WHEN TIMESTAMPDIFF(HOUR, o.fecha_recepcion, NOW()) < 24 
                                THEN CONCAT(TIMESTAMPDIFF(HOUR, o.fecha_recepcion, NOW()), ' h')
                                ELSE CONCAT(TIMESTAMPDIFF(DAY, o.fecha_recepcion, NOW()), ' d')
                            END as tiempo
                        FROM ordenes_servicio o
                        WHERE o.estado IN ('en_reparacion', 'pendiente', 'en_espera_repuestos')
                        AND o.fecha_recepcion >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                        ORDER BY o.fecha_recepcion DESC
                        LIMIT 4";
        
        // Consulta 3: Órdenes entregadas
        $sql_entregadas = "SELECT 
                                'Orden entregada' as evento,
                                CONCAT(c.nombre, ' - ', e.marca, ' ', e.modelo) as descripcion,
                                o.fecha_entrega as fecha,
                                CASE 
                                    WHEN TIMESTAMPDIFF(MINUTE, o.fecha_entrega, NOW()) < 60 
                                    THEN CONCAT(TIMESTAMPDIFF(MINUTE, o.fecha_entrega, NOW()), ' min')
                                    WHEN TIMESTAMPDIFF(HOUR, o.fecha_entrega, NOW()) < 24 
                                    THEN CONCAT(TIMESTAMPDIFF(HOUR, o.fecha_entrega, NOW()), ' h')
                                    ELSE CONCAT(TIMESTAMPDIFF(DAY, o.fecha_entrega, NOW()), ' d')
                                END as tiempo
                            FROM ordenes_servicio o
                            INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                            INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                            WHERE o.estado = 'entregado'
                            AND o.fecha_entrega >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                            ORDER BY o.fecha_entrega DESC
                            LIMIT 4";
        
        // Obtener resultados de cada consulta
        $resultados = [];
        
        $stmt1 = $this->db->getConnection()->query($sql_nuevas);
        $nuevas = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt2 = $this->db->getConnection()->query($sql_estados);
        $estados = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt3 = $this->db->getConnection()->query($sql_entregadas);
        $entregadas = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        
        // Combinar resultados
        $resultados = array_merge($nuevas, $estados, $entregadas);
        
        // Ordenar por fecha (más reciente primero)
        usort($resultados, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });
        
        // Limitar a los primeros 5
        return array_slice($resultados, 0, 5);
    }

    // Calcular porcentaje de cambio (para las tarjetas)
    public function getPorcentajeCambio($actual, $anterior) {
        if ($anterior == 0) return 0;
        return round((($actual - $anterior) / $anterior) * 100, 1);
    }

    // Obtener datos para gráfico de órdenes por estado
    public function getDatosGrafico() {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as cantidad
                FROM ordenes_servicio
                GROUP BY estado";
        return $this->db->getAll($sql);
    }
}
?>