<?php
// models/Equipo.php

require_once 'Model.php';

class Equipo extends Model {
    protected $table = 'equipos';
    protected $primaryKey = 'id_equipo';

    public function __construct() {
        parent::__construct();
    }

    // Obtener equipos con datos del cliente
    public function getEquiposConCliente() {
        $sql = "SELECT 
                    e.*,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email
                FROM equipos e
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                ORDER BY c.nombre ASC, e.marca ASC";
        return $this->db->getAll($sql);
    }

    // Obtener equipos de un cliente específico
    public function getEquiposByCliente($id_cliente) {
        $sql = "SELECT * FROM equipos WHERE id_cliente = ? ORDER BY marca, modelo";
        return $this->db->getAll($sql, [$id_cliente]);
    }

    // Obtener equipo por serial
    public function getBySerial($serial) {
        $sql = "SELECT * FROM {$this->table} WHERE serial = ?";
        return $this->db->getOne($sql, [$serial]);
    }

    // Verificar si un serial ya existe (excepto el mismo equipo)
    public function serialExiste($serial, $id_excluir = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE serial = ?";
        $params = [$serial];
        
        if ($id_excluir) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $id_excluir;
        }
        
        $result = $this->db->getOne($sql, $params);
        return $result['total'] > 0;
    }

    // Buscar equipos por marca, modelo o serial
    public function buscar($termino) {
        $sql = "SELECT 
                    e.*,
                    c.nombre as cliente_nombre
                FROM equipos e
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                WHERE e.marca LIKE ? 
                OR e.modelo LIKE ? 
                OR e.serial LIKE ?
                OR c.nombre LIKE ?
                ORDER BY e.marca ASC";
        $param = "%$termino%";
        return $this->db->getAll($sql, [$param, $param, $param, $param]);
    }

    // Verificar si un equipo tiene órdenes activas
    public function tieneOrdenesActivas($id_equipo) {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio 
                WHERE id_equipo = ? AND estado != 'entregado'";
        $result = $this->db->getOne($sql, [$id_equipo]);
        return $result['total'] > 0;
    }

    // Verificar si un equipo tiene órdenes en general
    public function tieneOrdenes($id_equipo) {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio 
                WHERE id_equipo = ?";
        $result = $this->db->getOne($sql, [$id_equipo]);
        return $result['total'] > 0;
    }

    // Obtener órdenes de un equipo
    public function getOrdenesEquipo($id_equipo) {
        $sql = "SELECT 
                    o.*,
                    t.nombre as tecnico_nombre,
                    o.total as total_orden
                FROM ordenes_servicio o
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                WHERE o.id_equipo = ?
                ORDER BY o.fecha_recepcion DESC";
        return $this->db->getAll($sql, [$id_equipo]);
    }

    // Obtener estadísticas de equipos por tipo
    public function getEstadisticasPorTipo() {
        $sql = "SELECT 
                    tipo,
                    COUNT(*) as cantidad
                FROM equipos
                GROUP BY tipo
                ORDER BY cantidad DESC";
        return $this->db->getAll($sql);
    }

    // Obtener todos los equipos con datos completos para reportes
    public function getEquiposCompletos() {
        $sql = "SELECT 
                    e.*,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email,
                    COUNT(o.id_orden) as total_ordenes,
                    SUM(CASE WHEN o.estado != 'entregado' THEN 1 ELSE 0 END) as ordenes_activas
                FROM equipos e
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                LEFT JOIN ordenes_servicio o ON e.id_equipo = o.id_equipo
                GROUP BY e.id_equipo
                ORDER BY e.marca ASC";
        return $this->db->getAll($sql);
    }
}
?>