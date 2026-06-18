<?php
// models/Usuario.php

require_once 'Model.php';

class Usuario extends Model {
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    public function __construct() {
        parent::__construct();
    }

    // Autenticar usuario por username y password
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE username = ? AND password = MD5(?)";
        return $this->db->getOne($sql, [$username, $password]);
    }

    // Verificar si el username ya existe
    public function exists($username) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE username = ?";
        $result = $this->db->getOne($sql, [$username]);
        return $result['total'] > 0;
    }

    // Obtener usuario por ID con datos completos
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->getOne($sql, [$id]);
    }
}
?>