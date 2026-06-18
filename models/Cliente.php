<?php
// models/Cliente.php

require_once 'Model.php';

class Cliente extends Model {
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    public function __construct() {
        parent::__construct();
    }

    // Obtener clientes con su conteo de equipos
    public function getClientesConEquipos() {
        $sql = "SELECT 
                    c.*,
                    COUNT(e.id_equipo) as total_equipos
                FROM clientes c
                LEFT JOIN equipos e ON c.id_cliente = e.id_cliente
                GROUP BY c.id_cliente
                ORDER BY c.nombre ASC";
        return $this->db->getAll($sql);
    }

    // Buscar clientes por nombre o email
    public function buscar($termino) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nombre LIKE ? 
                OR email LIKE ? 
                OR telefono LIKE ?
                ORDER BY nombre ASC";
        $param = "%$termino%";
        return $this->db->getAll($sql, [$param, $param, $param]);
    }

    // Obtener equipos de un cliente
    public function getEquiposCliente($id_cliente) {
        $sql = "SELECT * FROM equipos WHERE id_cliente = ? ORDER BY marca, modelo";
        return $this->db->getAll($sql, [$id_cliente]);
    }

    // Validar si el cliente tiene órdenes activas
    public function tieneOrdenesActivas($id_cliente) {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                WHERE e.id_cliente = ? AND o.estado != 'entregado'";
        $result = $this->db->getOne($sql, [$id_cliente]);
        return $result['total'] > 0;
    }

    // Validar email único (excepto el mismo cliente)
    public function emailExiste($email, $id_excluir = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($id_excluir) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $id_excluir;
        }
        
        $result = $this->db->getOne($sql, $params);
        return $result['total'] > 0;
    }
}
?>