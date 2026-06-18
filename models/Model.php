<?php
// models/Model.php

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Obtener todos los registros
    public function getAll() {
        return $this->db->getAll("SELECT * FROM {$this->table}");
    }

    // Obtener un registro por ID
    public function getById($id) {
        return $this->db->getOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    // Insertar un registro
    public function insert($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        return $this->db->insert($sql, array_values($data));
    }

    // Actualizar un registro
    public function update($id, $data) {
        $fields = array_keys($data);
        $set = implode(' = ?, ', $fields) . ' = ?';
        $sql = "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?";
        $params = array_merge(array_values($data), [$id]);
        return $this->db->query($sql, $params);
    }

    // Eliminar un registro
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$id]);
    }

    // Contar registros
    public function count($where = '') {
        return $this->db->count($this->table, $where);
    }

    // Buscar por campo
    public function findBy($field, $value) {
        return $this->db->getAll(
            "SELECT * FROM {$this->table} WHERE $field = ?",
            [$value]
        );
    }
}
?>