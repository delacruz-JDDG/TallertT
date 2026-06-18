<?php
// models/Repuesto.php

require_once 'Model.php';

class Repuesto extends Model {
    protected $table = 'repuestos';
    protected $primaryKey = 'id_repuesto';

    public function __construct() {
        parent::__construct();
    }

    // Obtener repuestos con estadísticas de uso
    public function getRepuestosConUso() {
        $sql = "SELECT 
                    r.*,
                    COUNT(ro.id_orden) as veces_usado,
                    SUM(ro.cantidad) as total_usado
                FROM repuestos r
                LEFT JOIN orden_repuestos ro ON r.id_repuesto = ro.id_repuesto
                GROUP BY r.id_repuesto
                ORDER BY r.nombre ASC";
        return $this->db->getAll($sql);
    }

    // Obtener repuestos con stock bajo (menos de 5 unidades)
    public function getStockBajo() {
        $sql = "SELECT * FROM {$this->table} WHERE stock <= 5 ORDER BY stock ASC";
        return $this->db->getAll($sql);
    }

    // Obtener repuestos con stock disponible (mayor a 0)
    public function getDisponibles() {
        $sql = "SELECT * FROM {$this->table} WHERE stock > 0 ORDER BY nombre ASC";
        return $this->db->getAll($sql);
    }

    // Buscar repuestos por nombre
    public function buscar($termino) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nombre LIKE ? 
                OR id_repuesto LIKE ?
                ORDER BY nombre ASC";
        $param = "%$termino%";
        return $this->db->getAll($sql, [$param, $param]);
    }

    // Verificar si un repuesto ha sido usado en órdenes
    public function haSidoUsado($id_repuesto) {
        $sql = "SELECT COUNT(*) as total 
                FROM orden_repuestos 
                WHERE id_repuesto = ?";
        $result = $this->db->getOne($sql, [$id_repuesto]);
        return $result['total'] > 0;
    }

    // Actualizar stock de un repuesto
    public function actualizarStock($id_repuesto, $cantidad, $operacion = 'restar') {
        // operacion: 'restar' o 'sumar'
        $sql = "UPDATE {$this->table} SET stock = stock " . 
               ($operacion == 'restar' ? '-' : '+') . " ? 
               WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$cantidad, $id_repuesto]);
    }

    // Verificar si hay suficiente stock
    public function tieneStock($id_repuesto, $cantidad) {
        $sql = "SELECT stock FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $result = $this->db->getOne($sql, [$id_repuesto]);
        return $result && $result['stock'] >= $cantidad;
    }

    // Obtener repuestos más usados
    public function getMasUsados($limit = 10) {
        $sql = "SELECT 
                    r.*,
                    COUNT(ro.id_orden) as veces_usado,
                    SUM(ro.cantidad) as total_usado
                FROM repuestos r
                INNER JOIN orden_repuestos ro ON r.id_repuesto = ro.id_repuesto
                GROUP BY r.id_repuesto
                ORDER BY veces_usado DESC
                LIMIT $limit";
        return $this->db->getAll($sql);
    }

    // Obtener repuestos por rango de precio
    public function getByPrecio($min, $max) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE precio_unitario BETWEEN ? AND ? 
                ORDER BY precio_unitario ASC";
        return $this->db->getAll($sql, [$min, $max]);
    }

    // Obtener resumen de stock total
    public function getResumenStock() {
        $sql = "SELECT 
                    COUNT(*) as total_repuestos,
                    SUM(stock) as stock_total,
                    SUM(precio_unitario * stock) as valor_inventario,
                    COUNT(CASE WHEN stock <= 5 THEN 1 END) as stock_bajo,
                    COUNT(CASE WHEN stock = 0 THEN 1 END) as agotados
                FROM repuestos";
        return $this->db->getOne($sql);
    }
}
?>