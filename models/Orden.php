<?php
// models/Orden.php

require_once 'Model.php';

class Orden extends Model {
    protected $table = 'ordenes_servicio';
    protected $primaryKey = 'id_orden';

    public function __construct() {
        parent::__construct();
    }

    // Obtener órdenes con todos los datos relacionados
    public function getOrdenesCompletas() {
        $sql = "SELECT 
                    o.*,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email,
                    e.marca,
                    e.modelo,
                    e.serial,
                    e.tipo as equipo_tipo,
                    t.nombre as tecnico_nombre,
                    t.especialidad,
                    (SELECT SUM(ro.cantidad * r.precio_unitario) 
                     FROM orden_repuestos ro 
                     INNER JOIN repuestos r ON ro.id_repuesto = r.id_repuesto 
                     WHERE ro.id_orden = o.id_orden) as total_repuestos
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                ORDER BY o.fecha_recepcion DESC";
        return $this->db->getAll($sql);
    }

    // Obtener una orden con todos sus detalles
    public function getOrdenCompleta($id_orden) {
        $sql = "SELECT 
                    o.*,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email,
                    c.direccion as cliente_direccion,
                    c.tipo as cliente_tipo,
                    e.id_equipo,
                    e.marca,
                    e.modelo,
                    e.serial,
                    e.tipo as equipo_tipo,
                    t.id_tecnico,
                    t.nombre as tecnico_nombre,
                    t.especialidad,
                    t.telefono as tecnico_telefono,
                    (SELECT SUM(ro.cantidad * r.precio_unitario) 
                     FROM orden_repuestos ro 
                     INNER JOIN repuestos r ON ro.id_repuesto = r.id_repuesto 
                     WHERE ro.id_orden = o.id_orden) as total_repuestos
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                WHERE o.id_orden = ?";
        return $this->db->getOne($sql, [$id_orden]);
    }

    // Obtener repuestos de una orden
    public function getRepuestosOrden($id_orden) {
        $sql = "SELECT 
                    ro.*,
                    r.nombre,
                    r.precio_unitario,
                    (ro.cantidad * r.precio_unitario) as subtotal
                FROM orden_repuestos ro
                INNER JOIN repuestos r ON ro.id_repuesto = r.id_repuesto
                WHERE ro.id_orden = ?
                ORDER BY r.nombre ASC";
        return $this->db->getAll($sql, [$id_orden]);
    }

    // Obtener órdenes por estado
    public function getByEstado($estado) {
        $sql = "SELECT 
                    o.*,
                    c.nombre as cliente_nombre,
                    e.marca,
                    e.modelo,
                    t.nombre as tecnico_nombre
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                WHERE o.estado = ?
                ORDER BY o.fecha_recepcion DESC";
        return $this->db->getAll($sql, [$estado]);
    }

    // Obtener órdenes por técnico
    public function getByTecnico($id_tecnico) {
        $sql = "SELECT 
                    o.*,
                    c.nombre as cliente_nombre,
                    e.marca,
                    e.modelo,
                    t.nombre as tecnico_nombre
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                WHERE o.id_tecnico = ?
                ORDER BY o.fecha_recepcion DESC";
        return $this->db->getAll($sql, [$id_tecnico]);
    }

    // Obtener órdenes por cliente
    public function getByCliente($id_cliente) {
        $sql = "SELECT 
                    o.*,
                    c.nombre as cliente_nombre,
                    e.marca,
                    e.modelo,
                    t.nombre as tecnico_nombre
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                INNER JOIN tecnicos t ON o.id_tecnico = t.id_tecnico
                WHERE c.id_cliente = ?
                ORDER BY o.fecha_recepcion DESC";
        return $this->db->getAll($sql, [$id_cliente]);
    }

    // Verificar si un equipo tiene una orden activa
    public function equipoTieneOrdenActiva($id_equipo, $id_orden_excluir = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM ordenes_servicio 
                WHERE id_equipo = ? AND estado != 'entregado'";
        $params = [$id_equipo];
        
        if ($id_orden_excluir) {
            $sql .= " AND id_orden != ?";
            $params[] = $id_orden_excluir;
        }
        
        $result = $this->db->getOne($sql, $params);
        return $result['total'] > 0;
    }

    // Agregar repuesto a una orden (con validación de stock)
    public function agregarRepuesto($id_orden, $id_repuesto, $cantidad) {
        // Iniciar transacción
        $this->db->beginTransaction();
        
        try {
            // Verificar stock disponible
            $sql_stock = "SELECT stock FROM repuestos WHERE id_repuesto = ?";
            $stock_result = $this->db->getOne($sql_stock, [$id_repuesto]);
            
            if (!$stock_result || $stock_result['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente para el repuesto");
            }
            
            // Verificar si el repuesto ya está en la orden
            $sql_check = "SELECT cantidad FROM orden_repuestos 
                          WHERE id_orden = ? AND id_repuesto = ?";
            $check = $this->db->getOne($sql_check, [$id_orden, $id_repuesto]);
            
            if ($check) {
                // Actualizar cantidad
                $nueva_cantidad = $check['cantidad'] + $cantidad;
                $sql_update = "UPDATE orden_repuestos 
                               SET cantidad = ? 
                               WHERE id_orden = ? AND id_repuesto = ?";
                $this->db->query($sql_update, [$nueva_cantidad, $id_orden, $id_repuesto]);
            } else {
                // Insertar nuevo repuesto
                $sql_insert = "INSERT INTO orden_repuestos (id_orden, id_repuesto, cantidad) 
                               VALUES (?, ?, ?)";
                $this->db->query($sql_insert, [$id_orden, $id_repuesto, $cantidad]);
            }
            
            // Actualizar stock
            $sql_stock_update = "UPDATE repuestos SET stock = stock - ? 
                                 WHERE id_repuesto = ?";
            $this->db->query($sql_stock_update, [$cantidad, $id_repuesto]);
            
            // Recalcular total de la orden
            $this->recalcularTotal($id_orden);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // Eliminar repuesto de una orden (devolver al stock)
    public function eliminarRepuesto($id_orden, $id_repuesto) {
        $this->db->beginTransaction();
        
        try {
            // Obtener cantidad actual
            $sql_get = "SELECT cantidad FROM orden_repuestos 
                        WHERE id_orden = ? AND id_repuesto = ?";
            $result = $this->db->getOne($sql_get, [$id_orden, $id_repuesto]);
            
            if (!$result) {
                throw new Exception("Repuesto no encontrado en la orden");
            }
            
            $cantidad = $result['cantidad'];
            
            // Eliminar de la orden
            $sql_delete = "DELETE FROM orden_repuestos 
                           WHERE id_orden = ? AND id_repuesto = ?";
            $this->db->query($sql_delete, [$id_orden, $id_repuesto]);
            
            // Devolver al stock
            $sql_stock = "UPDATE repuestos SET stock = stock + ? 
                          WHERE id_repuesto = ?";
            $this->db->query($sql_stock, [$cantidad, $id_repuesto]);
            
            // Recalcular total
            $this->recalcularTotal($id_orden);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // Recalcular total de la orden
    public function recalcularTotal($id_orden) {
        // Calcular total de repuestos
        $sql_repuestos = "SELECT SUM(ro.cantidad * r.precio_unitario) as total_repuestos
                          FROM orden_repuestos ro
                          INNER JOIN repuestos r ON ro.id_repuesto = r.id_repuesto
                          WHERE ro.id_orden = ?";
        $result = $this->db->getOne($sql_repuestos, [$id_orden]);
        $total_repuestos = $result['total_repuestos'] ?? 0;
        
        // Obtener mano de obra
        $sql_orden = "SELECT mano_obra FROM ordenes_servicio WHERE id_orden = ?";
        $orden = $this->db->getOne($sql_orden, [$id_orden]);
        $mano_obra = $orden['mano_obra'] ?? 0;
        
        // Calcular total
        $total = $total_repuestos + $mano_obra;
        
        // Actualizar
        $sql_update = "UPDATE ordenes_servicio SET total = ? WHERE id_orden = ?";
        $this->db->query($sql_update, [$total, $id_orden]);
        
        return $total;
    }

    // Cambiar estado de la orden (con validaciones)
    public function cambiarEstado($id_orden, $nuevo_estado) {
        // Obtener estado actual
        $sql_estado = "SELECT estado FROM ordenes_servicio WHERE id_orden = ?";
        $result = $this->db->getOne($sql_estado, [$id_orden]);
        $estado_actual = $result['estado'] ?? null;
        
        if (!$estado_actual) {
            throw new Exception("Orden no encontrada");
        }
        
        // Validaciones de negocio
        if ($nuevo_estado == 'entregado' && 
            in_array($estado_actual, ['en_diagnostico', 'en_espera_repuestos'])) {
            throw new Exception("No se puede entregar una orden que está en diagnóstico o en espera de repuestos");
        }
        
        // Si se entrega, registrar fecha de entrega
        $fecha_entrega = ($nuevo_estado == 'entregado') ? date('Y-m-d H:i:s') : null;
        
        $sql_update = "UPDATE ordenes_servicio 
                       SET estado = ?, fecha_entrega = ? 
                       WHERE id_orden = ?";
        $this->db->query($sql_update, [$nuevo_estado, $fecha_entrega, $id_orden]);
        
        return true;
    }

    // Reporte de ingresos por técnico en un período
    public function reporteIngresosTecnico($id_tecnico, $fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    o.id_orden,
                    o.fecha_recepcion,
                    o.fecha_entrega,
                    o.total,
                    c.nombre as cliente_nombre,
                    e.marca,
                    e.modelo,
                    (SELECT COUNT(*) FROM orden_repuestos ro 
                     WHERE ro.id_orden = o.id_orden) as total_repuestos
                FROM ordenes_servicio o
                INNER JOIN equipos e ON o.id_equipo = e.id_equipo
                INNER JOIN clientes c ON e.id_cliente = c.id_cliente
                WHERE o.id_tecnico = ? 
                    AND o.estado = 'entregado'
                    AND o.fecha_entrega BETWEEN ? AND ?
                ORDER BY o.fecha_entrega DESC";
        return $this->db->getAll($sql, [$id_tecnico, $fecha_inicio, $fecha_fin]);
    }

    // Calcular total de ingresos por técnico
    public function totalIngresosTecnico($id_tecnico, $fecha_inicio, $fecha_fin) {
        $sql = "SELECT SUM(total) as total_ingresos
                FROM ordenes_servicio
                WHERE id_tecnico = ? 
                    AND estado = 'entregado'
                    AND fecha_entrega BETWEEN ? AND ?";
        $result = $this->db->getOne($sql, [$id_tecnico, $fecha_inicio, $fecha_fin]);
        return $result['total_ingresos'] ?? 0;
    }

    // Obtener estadísticas de órdenes
    public function getEstadisticas() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregadas,
                    SUM(CASE WHEN estado = 'en_reparacion' THEN 1 ELSE 0 END) as en_reparacion,
                    SUM(CASE WHEN estado = 'en_diagnostico' THEN 1 ELSE 0 END) as en_diagnostico,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en_espera_repuestos' THEN 1 ELSE 0 END) as en_espera,
                    SUM(total) as total_ingresos,
                    AVG(total) as promedio
                FROM ordenes_servicio";
        return $this->db->getOne($sql);
    }
}
?>