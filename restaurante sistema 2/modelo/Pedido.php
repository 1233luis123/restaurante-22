
<?php
/**
 * Modelo Pedido
 * Gestiona todas las operaciones relacionadas con pedidos y delivery
 */

require_once __DIR__ . '/../config/conexion.php';

class Pedido {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    /**
     * Crear nuevo pedido con sus detalles
     */
    public function crear($datos, $detalles) {
        try {
            // Iniciar transacción
            $this->conexion->beginTransaction();
            
            // Insertar pedido principal
            $sql = "INSERT INTO pedido (usuario_id, direccion_entrega, telefono, total, notas, estado) 
                    VALUES (:usuario_id, :direccion, :telefono, :total, :notas, 'pendiente')";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $datos['usuario_id'],
                ':direccion' => $datos['direccion_entrega'],
                ':telefono' => $datos['telefono'],
                ':total' => $datos['total'],
                ':notas' => $datos['notas'] ?? null
            ]);
            
            $pedido_id = $this->conexion->lastInsertId();
            
            // Insertar detalles del pedido
            $sqlDetalle = "INSERT INTO detalle_pedido (pedido_id, plato_id, cantidad, precio_unitario, subtotal) 
                          VALUES (:pedido_id, :plato_id, :cantidad, :precio_unitario, :subtotal)";
            
            $stmtDetalle = $this->conexion->prepare($sqlDetalle);
            
            foreach ($detalles as $detalle) {
                $stmtDetalle->execute([
                    ':pedido_id' => $pedido_id,
                    ':plato_id' => $detalle['plato_id'],
                    ':cantidad' => $detalle['cantidad'],
                    ':precio_unitario' => $detalle['precio_unitario'],
                    ':subtotal' => $detalle['subtotal']
                ]);
            }
            
            // Confirmar transacción
            $this->conexion->commit();
            
            return [
                'exito' => true,
                'mensaje' => 'Pedido creado exitosamente',
                'id_pedido' => $pedido_id
            ];
            
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $this->conexion->rollBack();
            
            return [
                'exito' => false,
                'mensaje' => 'Error al crear pedido: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Listar todos los pedidos con filtros
     */
    public function listarTodos($filtros = []) {
        try {
            $sql = "SELECT p.*, u.nombre as usuario_nombre, u.correo as usuario_correo
                    FROM pedido p
                    INNER JOIN usuario u ON p.usuario_id = u.id_usuario
                    WHERE 1=1";
            $params = [];
            
            // Filtrar por estado
            if (!empty($filtros['estado'])) {
                $sql .= " AND p.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            // Filtrar por usuario
            if (!empty($filtros['usuario_id'])) {
                $sql .= " AND p.usuario_id = :usuario_id";
                $params[':usuario_id'] = $filtros['usuario_id'];
            }
            
            // Filtrar por fecha
            if (!empty($filtros['fecha_desde'])) {
                $sql .= " AND DATE(p.fecha_pedido) >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $sql .= " AND DATE(p.fecha_pedido) <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'];
            }
            
            $sql .= " ORDER BY p.fecha_pedido DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al listar pedidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener pedido por ID con sus detalles
     */
    public function obtenerPorId($id) {
        try {
            // Obtener datos del pedido
            $sql = "SELECT p.*, u.nombre as usuario_nombre, u.correo as usuario_correo, u.telefono as usuario_telefono
                    FROM pedido p
                    INNER JOIN usuario u ON p.usuario_id = u.id_usuario
                    WHERE p.id_pedido = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            $pedido = $stmt->fetch();
            
            if (!$pedido) {
                return null;
            }
            
            // Obtener detalles del pedido
            $sqlDetalles = "SELECT dp.*, pl.nombre as plato_nombre, pl.imagen as plato_imagen
                           FROM detalle_pedido dp
                           INNER JOIN plato pl ON dp.plato_id = pl.id_plato
                           WHERE dp.pedido_id = :id";
            
            $stmtDetalles = $this->conexion->prepare($sqlDetalles);
            $stmtDetalles->execute([':id' => $id]);
            $pedido['detalles'] = $stmtDetalles->fetchAll();
            
            return $pedido;
            
        } catch (PDOException $e) {
            error_log("Error al obtener pedido: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Actualizar estado del pedido
     */
    public function actualizarEstado($id, $estado) {
        try {
            $estadosValidos = ['pendiente', 'preparando', 'enviado', 'entregado', 'cancelado'];
            
            if (!in_array($estado, $estadosValidos)) {
                return [
                    'exito' => false,
                    'mensaje' => 'Estado no válido'
                ];
            }
            
            $sql = "UPDATE pedido SET estado = :estado WHERE id_pedido = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':estado' => $estado,
                ':id' => $id
            ]);
            
            return [
                'exito' => true,
                'mensaje' => 'Estado actualizado a: ' . ucfirst($estado)
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar estado: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cancelar pedido
     */
    public function cancelar($id, $usuario_id = null) {
        try {
            // Verificar que el pedido pertenece al usuario (si se proporciona)
            if ($usuario_id) {
                $sqlCheck = "SELECT usuario_id, estado FROM pedido WHERE id_pedido = :id";
                $stmt = $this->conexion->prepare($sqlCheck);
                $stmt->execute([':id' => $id]);
                $pedido = $stmt->fetch();
                
                if (!$pedido) {
                    return [
                        'exito' => false,
                        'mensaje' => 'Pedido no encontrado'
                    ];
                }
                
                if ($pedido['usuario_id'] != $usuario_id) {
                    return [
                        'exito' => false,
                        'mensaje' => 'No tiene permisos para cancelar este pedido'
                    ];
                }
                
                // No permitir cancelar si ya está enviado o entregado
                if (in_array($pedido['estado'], ['enviado', 'entregado'])) {
                    return [
                        'exito' => false,
                        'mensaje' => 'No se puede cancelar un pedido que ya está ' . $pedido['estado']
                    ];
                }
            }
            
            $sql = "UPDATE pedido SET estado = 'cancelado' WHERE id_pedido = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return [
                'exito' => true,
                'mensaje' => 'Pedido cancelado exitosamente'
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al cancelar pedido: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener pedidos de un usuario
     */
    public function obtenerPorUsuario($usuario_id) {
        try {
            $sql = "SELECT p.*, 
                    (SELECT COUNT(*) FROM detalle_pedido WHERE pedido_id = p.id_pedido) as total_items
                    FROM pedido p
                    WHERE p.usuario_id = :usuario_id
                    ORDER BY p.fecha_pedido DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':usuario_id' => $usuario_id]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos de usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener pedidos recientes
     */
    public function obtenerRecientes($limite = 10) {
        try {
            $sql = "SELECT p.*, u.nombre as usuario_nombre
                    FROM pedido p
                    INNER JOIN usuario u ON p.usuario_id = u.id_usuario
                    ORDER BY p.fecha_pedido DESC
                    LIMIT :limite";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos recientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener pedidos activos (pendientes, preparando, enviado)
     */
    public function obtenerActivos() {
        try {
            $sql = "SELECT p.*, u.nombre as usuario_nombre, u.telefono as usuario_telefono
                    FROM pedido p
                    INNER JOIN usuario u ON p.usuario_id = u.id_usuario
                    WHERE p.estado IN ('pendiente', 'preparando', 'enviado')
                    ORDER BY p.fecha_pedido DESC";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos activos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de pedidos
     */
    public function obtenerEstadisticas($periodo = 'mes') {
        try {
            $condicionFecha = "MONTH(fecha_pedido) = MONTH(CURRENT_DATE()) AND YEAR(fecha_pedido) = YEAR(CURRENT_DATE())";
            
            if ($periodo === 'dia') {
                $condicionFecha = "DATE(fecha_pedido) = CURRENT_DATE()";
            } elseif ($periodo === 'semana') {
                $condicionFecha = "YEARWEEK(fecha_pedido) = YEARWEEK(CURRENT_DATE())";
            } elseif ($periodo === 'anio') {
                $condicionFecha = "YEAR(fecha_pedido) = YEAR(CURRENT_DATE())";
            }
            
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'preparando' THEN 1 ELSE 0 END) as preparando,
                    SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as enviados,
                    SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
                    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(CASE WHEN estado = 'entregado' THEN total ELSE 0 END) as ingresos_total,
                    AVG(CASE WHEN estado = 'entregado' THEN total ELSE NULL END) as ticket_promedio
                    FROM pedido
                    WHERE $condicionFecha";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error en estadísticas de pedidos: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calcular total de un pedido desde sus detalles
     */
    public function calcularTotal($detalles) {
        $total = 0;
        foreach ($detalles as $detalle) {
            $total += $detalle['cantidad'] * $detalle['precio_unitario'];
        }
        return $total;
    }
    
    /**
     * Obtener ventas por día (últimos 7 días)
     */
    public function ventasPorDia($dias = 7) {
        try {
            $sql = "SELECT 
                    DATE(fecha_pedido) as fecha,
                    COUNT(*) as total_pedidos,
                    SUM(total) as total_ventas
                    FROM pedido
                    WHERE fecha_pedido >= DATE_SUB(CURRENT_DATE(), INTERVAL :dias DAY)
                    AND estado = 'entregado'
                    GROUP BY DATE(fecha_pedido)
                    ORDER BY fecha DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':dias', $dias, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener ventas por día: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener pedidos por rango de fechas
     */
    public function obtenerPorRangoFechas($fecha_inicio, $fecha_fin) {
        try {
            $sql = "SELECT p.*, u.nombre as usuario_nombre
                    FROM pedido p
                    INNER JOIN usuario u ON p.usuario_id = u.id_usuario
                    WHERE DATE(p.fecha_pedido) BETWEEN :fecha_inicio AND :fecha_fin
                    ORDER BY p.fecha_pedido DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin
            ]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos por rango: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verificar si un pedido pertenece a un usuario
     */
    public function perteneceAUsuario($pedido_id, $usuario_id) {
        try {
            $sql = "SELECT COUNT(*) as existe FROM pedido 
                    WHERE id_pedido = :pedido_id AND usuario_id = :usuario_id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':pedido_id' => $pedido_id,
                ':usuario_id' => $usuario_id
            ]);
            
            $resultado = $stmt->fetch();
            return $resultado['existe'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar pertenencia: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener total de ingresos
     */
    public function obtenerTotalIngresos($periodo = 'mes') {
        try {
            $condicionFecha = "MONTH(fecha_pedido) = MONTH(CURRENT_DATE()) AND YEAR(fecha_pedido) = YEAR(CURRENT_DATE())";
            
            if ($periodo === 'dia') {
                $condicionFecha = "DATE(fecha_pedido) = CURRENT_DATE()";
            } elseif ($periodo === 'semana') {
                $condicionFecha = "YEARWEEK(fecha_pedido) = YEARWEEK(CURRENT_DATE())";
            } elseif ($periodo === 'anio') {
                $condicionFecha = "YEAR(fecha_pedido) = YEAR(CURRENT_DATE())";
            }
            
            $sql = "SELECT SUM(total) as total_ingresos
                    FROM pedido
                    WHERE estado = 'entregado' AND $condicionFecha";
            
            $stmt = $this->conexion->query($sql);
            $resultado = $stmt->fetch();
            return $resultado['total_ingresos'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Error al obtener ingresos: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener platos más vendidos
     */
    public function platosMasVendidos($limite = 10) {
        try {
            $sql = "SELECT pl.nombre, pl.categoria, 
                    SUM(dp.cantidad) as total_vendido,
                    SUM(dp.subtotal) as ingresos_generados
                    FROM detalle_pedido dp
                    INNER JOIN plato pl ON dp.plato_id = pl.id_plato
                    INNER JOIN pedido p ON dp.pedido_id = p.id_pedido
                    WHERE p.estado = 'entregado'
                    GROUP BY pl.id_plato
                    ORDER BY total_vendido DESC
                    LIMIT :limite";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener platos más vendidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener historial completo de un pedido (para tracking)
     */
    public function obtenerHistorial($pedido_id) {
        try {
            $sql = "SELECT * FROM pedido WHERE id_pedido = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $pedido_id]);
            $pedido = $stmt->fetch();
            
            if (!$pedido) {
                return null;
            }
            
            // Construir historial basado en el estado actual
            $historial = [
                'pendiente' => ['fecha' => $pedido['fecha_pedido'], 'completado' => true],
                'preparando' => ['fecha' => null, 'completado' => false],
                'enviado' => ['fecha' => null, 'completado' => false],
                'entregado' => ['fecha' => null, 'completado' => false]
            ];
            
            $estados = ['pendiente', 'preparando', 'enviado', 'entregado'];
            $indiceActual = array_search($pedido['estado'], $estados);
            
            if ($indiceActual !== false) {
                for ($i = 0; $i <= $indiceActual; $i++) {
                    $historial[$estados[$i]]['completado'] = true;
                }
            }
            
            return [
                'pedido' => $pedido,
                'historial' => $historial
            ];
            
        } catch (PDOException $e) {
            error_log("Error al obtener historial: " . $e->getMessage());
            return null;
        }
    }

    /**
 * Obtener detalle completo de un pedido con sus items
 * @param int $id_pedido ID del pedido
 * @return array|null Datos del pedido con sus detalles o null si no existe
 */
public function obtenerDetallePedido($id_pedido) {
    try {
        // Usar la conexión existente de la clase
        $conexion = $this->conexion;
        
        // Obtener información del pedido
        // NOTA: Las tablas se llaman 'pedido' y 'usuario' (sin 's')
        $sql = "SELECT p.*, u.nombre as usuario_nombre, u.correo as usuario_correo
                FROM pedido p
                INNER JOIN usuario u ON p.usuario_id = u.id_usuario
                WHERE p.id_pedido = :id_pedido";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id_pedido' => $id_pedido]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido) {
            error_log("Pedido #$id_pedido no encontrado");
            return null;
        }
        
        error_log("Pedido #$id_pedido encontrado, obteniendo detalles...");
        
        // Obtener detalles (items del pedido)
        // NOTA: Las tablas se llaman 'plato' (sin 's')
        $sql = "SELECT dp.*, pl.nombre as plato_nombre
                FROM detalle_pedido dp
                INNER JOIN plato pl ON dp.plato_id = pl.id_plato
                WHERE dp.pedido_id = :id_pedido
                ORDER BY dp.id_detalle";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id_pedido' => $id_pedido]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Se encontraron " . count($detalles) . " items en el pedido");
        
        $pedido['detalles'] = $detalles;
        
        return $pedido;
        
    } catch (PDOException $e) {
        error_log("Error al obtener detalle del pedido: " . $e->getMessage());
        return null;
    }
}
}
?>