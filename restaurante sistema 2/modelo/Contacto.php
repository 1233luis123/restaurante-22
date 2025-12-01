
<?php
/**
 * Modelo Contacto
 * Gestiona todas las operaciones relacionadas con mensajes de contacto
 */

require_once __DIR__ . '/../config/conexion.php';

class Contacto {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    /**
     * Crear nuevo mensaje de contacto
     */
    public function crear($datos) {
        try {
            $sql = "INSERT INTO contacto (nombre, correo, telefono, mensaje) 
                    VALUES (:nombre, :correo, :telefono, :mensaje)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':correo' => $datos['correo'],
                ':telefono' => $datos['telefono'] ?? null,
                ':mensaje' => $datos['mensaje']
            ]);
            
            return [
                'exito' => true,
                'mensaje' => 'Mensaje enviado exitosamente. Nos pondremos en contacto pronto',
                'id' => $this->conexion->lastInsertId()
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al enviar mensaje: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Listar todos los mensajes
     */
    public function listarTodos($filtros = []) {
        try {
            $sql = "SELECT * FROM contacto WHERE 1=1";
            $params = [];
            
            // Filtrar por leído/no leído
            if (isset($filtros['leido'])) {
                $sql .= " AND leido = :leido";
                $params[':leido'] = $filtros['leido'];
            }
            
            $sql .= " ORDER BY fecha_envio DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al listar mensajes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener mensaje por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM contacto WHERE id_contacto = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener mensaje: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Marcar como leído
     */
    public function marcarLeido($id) {
        try {
            $sql = "UPDATE contacto SET leido = 1 WHERE id_contacto = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return [
                'exito' => true,
                'mensaje' => 'Mensaje marcado como leído'
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar mensaje: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Eliminar mensaje
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM contacto WHERE id_contacto = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return [
                'exito' => true,
                'mensaje' => 'Mensaje eliminado exitosamente'
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al eliminar mensaje: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener estadísticas de mensajes
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN leido = 0 THEN 1 ELSE 0 END) as no_leidos,
                    SUM(CASE WHEN leido = 1 THEN 1 ELSE 0 END) as leidos
                    FROM contacto";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error en estadísticas: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener mensajes no leídos
     */
    public function obtenerNoLeidos($limite = 10) {
        try {
            $sql = "SELECT * FROM contacto 
                    WHERE leido = 0 
                    ORDER BY fecha_envio DESC 
                    LIMIT :limite";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener mensajes no leídos: " . $e->getMessage());
            return [];
        }
    }
}
?>