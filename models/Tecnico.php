<?php
// models/Tecnico.php

require_once 'Model.php';

class Tecnico extends Model {
    protected $table = 'tecnicos';
    protected $primaryKey = 'id_tecnico';

    public function __construct() {
        parent::__construct();
    }

    // Obtener técnicos con conteo de órdenes asignadas
    public function getTecnicosConOrdenes() {
        $sql = "SELECT 
                    t.*,
                    COUNT(o.id_orden) as total_ordenes,
                    SUM(CASE WHEN o.estado != 'entregado' THEN 1 ELSE 0 END) as ordenes_activas
                FROM tecnicos t
                LEFT JOIN ordenes_servicio o ON t.id_tecnico = o.id_tecnico
                GROUP BY t.id_tecnico
                ORDER BY t.nombre ASC";
        return $this->db->getAll($sql);
    }

    // Obtener solo técnicos activos
    public function getActivos() {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 'activo' ORDER BY nombre ASC";
        return $this->db->getAll($sql);
    }

    // Buscar técnicos por nombre o especialidad
    public function buscar($termino) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nombre LIKE ? 
                OR especialidad LIKE ? 
                OR telefono LIKE ?
                ORDER BY nombre ASC";
        $param = "%$termino%";
        return $this->db->getAll($sql, [$param, $param, $param]);
    }

    // Verificar si un técnico tiene órdenes activas
    public function tieneOrdenesActivas($id_tecnico) {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio 
                WHERE id_tecnico = ? AND estado != 'entregado'";
        $result = $this->db->getOne($sql, [$id_tecnico]);
        return $result['total'] > 0;
    }

    // Verificar si un técnico tiene órdenes en general
    public function tieneOrdenes($id_tecnico) {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio 
                WHERE id_tecnico = ?";
        $result = $this->db->getOne($sql, [$id_tecnico]);
        return $result['total'] > 0;
    }

    // Obtener órdenes de un técnico
    public function getOrdenesTecnico($id_tecnico, $limit = 10) {
        $sql = "SELECT 
                    o.*,
                    c.nombre as cliente_nombre,
                    e.marca,
                    e.modelo,
                    e.serial
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                WHERE o.id_tecnico = ?
                ORDER BY o.fecha_recepcion DESC
                LIMIT $limit";
        return $this->db->getAll($sql, [$id_tecnico]);
    }

    // Cambiar estado del técnico (activar/desactivar)
    public function cambiarEstado($id_tecnico, $estado) {
        $sql = "UPDATE {$this->table} SET estado = ? WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$estado, $id_tecnico]);
    }
}
?>