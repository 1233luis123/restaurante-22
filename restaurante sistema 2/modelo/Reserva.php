
<?php
/**
 * Modelo Reserva
 * Gestiona todas las operaciones relacionadas con reservas
 */

require_once __DIR__ . '/../config/conexion.php';

class Reserva {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    /**
     * Crear nueva reserva
     */
    public function crear($datos) {
        try {
            $sql = "INSERT INTO reserva (usuario_id, nombre, telefono, correo, fecha, hora, personas, mensaje, estado) 
                    VALUES (:usuario_id, :nombre, :telefono, :correo, :fecha, :hora, :personas, :mensaje, 'pendiente')";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $datos['usuario_id'] ?? null,
                ':nombre' => $datos['nombre'],
                ':telefono' => $datos['telefono'],
                ':correo' => $datos['correo'],
                ':fecha' => $datos['fecha'],
                ':hora' => $datos['hora'],
                ':personas' => $datos['personas'],
                ':mensaje' => $datos['mensaje'] ?? null
            ]);
            
            return [
                'exito' => true,
                'mensaje' => 'Reserva creada exitosamente. Le confirmaremos por correo',
                'id' => $this->conexion->lastInsertId()
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al crear reserva: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Listar todas las reservas
     */
    public function listarTodas($filtros = []) {
        try {
            $sql = "SELECT r.*, u.nombre as usuario_nombre 
                    FROM reserva r
                    LEFT JOIN usuario u ON r.usuario_id = u.id_usuario
                    WHERE 1=1";
            $params = [];
            
            // Filtrar por estado
            if (!empty($filtros['estado'])) {
                $sql .= " AND r.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            // Filtrar por fecha
            if (!empty($filtros['fecha'])) {
                $sql .= " AND r.fecha = :fecha";
                $params[':fecha'] = $filtros['fecha'];
            }
            
            // Filtrar por usuario
            if (!empty($filtros['usuario_id'])) {
                $sql .= " AND r.usuario_id = :usuario_id";
                $params[':usuario_id'] = $filtros['usuario_id'];
            }
            
            $sql .= " ORDER BY r.fecha DESC, r.hora DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al listar reservas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener reserva por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT r.*, u.nombre as usuario_nombre 
                    FROM reserva r
                    LEFT JOIN usuario u ON r.usuario_id = u.id_usuario
                    WHERE r.id_reserva = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener reserva: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Actualizar estado de reserva
     */
    public function actualizarEstado($id, $estado) {
        try {
            $sql = "UPDATE reserva SET estado = :estado WHERE id_reserva = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':estado' => $estado,
                ':id' => $id
            ]);
            
            return [
                'exito' => true,
                'mensaje' => 'Estado de reserva actualizado'
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar estado: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cancelar reserva
     */
    public function cancelar($id, $usuario_id = null) {
        try {
            // Verificar que la reserva pertenece al usuario (si se proporciona)
            if ($usuario_id) {
                $sqlCheck = "SELECT usuario_id FROM reserva WHERE id_reserva = :id";
                $stmt = $this->conexion->prepare($sqlCheck);
                $stmt->execute([':id' => $id]);
                $reserva = $stmt->fetch();
                
                if ($reserva && $reserva['usuario_id'] != $usuario_id) {
                    return [
                        'exito' => false,
                        'mensaje' => 'No tiene permisos para cancelar esta reserva'
                    ];
                }
            }
            
            $sql = "UPDATE reserva SET estado = 'cancelada' WHERE id_reserva = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return [
                'exito' => true,
                'mensaje' => 'Reserva cancelada exitosamente'
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al cancelar reserva: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verificar disponibilidad
     */
    public function verificarDisponibilidad($fecha, $hora) {
        try {
            $sql = "SELECT COUNT(*) as total FROM reserva 
                    WHERE fecha = :fecha AND hora = :hora 
                    AND estado IN ('pendiente', 'confirmada')";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':fecha' => $fecha,
                ':hora' => $hora
            ]);
            
            $resultado = $stmt->fetch();
            $capacidad_maxima = 10; // Número máximo de reservas por horario
            
            return [
                'disponible' => $resultado['total'] < $capacidad_maxima,
                'reservas_actuales' => $resultado['total'],
                'capacidad_maxima' => $capacidad_maxima
            ];
            
        } catch (PDOException $e) {
            error_log("Error al verificar disponibilidad: " . $e->getMessage());
            return ['disponible' => false];
        }
    }
    
    /**
     * Obtener reservas de un usuario
     */
    public function obtenerPorUsuario($usuario_id) {
        try {
            $sql = "SELECT * FROM reserva 
                    WHERE usuario_id = :usuario_id 
                    ORDER BY fecha DESC, hora DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener reservas de usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Estadísticas de reservas
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END) as confirmadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                    SUM(personas) as total_personas
                    FROM reserva
                    WHERE MONTH(fecha) = MONTH(CURRENT_DATE())
                    AND YEAR(fecha) = YEAR(CURRENT_DATE())";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error en estadísticas: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Reservas de hoy
     */
    public function reservasHoy() {
        try {
            $sql = "SELECT r.*, u.nombre as usuario_nombre 
                    FROM reserva r
                    LEFT JOIN usuario u ON r.usuario_id = u.id_usuario
                    WHERE r.fecha = CURRENT_DATE()
                    AND r.estado IN ('pendiente', 'confirmada')
                    ORDER BY r.hora";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener reservas de hoy: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Próximas reservas
     */
    public function proximasReservas($limite = 10) {
        try {
            $sql = "SELECT r.*, u.nombre as usuario_nombre 
                    FROM reserva r
                    LEFT JOIN usuario u ON r.usuario_id = u.id_usuario
                    WHERE r.fecha >= CURRENT_DATE()
                    AND r.estado IN ('pendiente', 'confirmada')
                    ORDER BY r.fecha, r.hora
                    LIMIT :limite";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener próximas reservas: " . $e->getMessage());
            return [];
        }
    }
}
?>