<?php
/**
 * Modelo Plato
 * Gestiona todas las operaciones relacionadas con platos
 */

require_once __DIR__ . '/../config/conexion.php';

class Plato {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    /**
     * Subir imagen y retornar el nombre del archivo
     */
    private function subirImagen($archivo) {
        // Directorio donde se guardarán las imágenes
        $directorioDestino = __DIR__ . '/../assets/img/uploads/';
        
        // Crear el directorio si no existe
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }
        
        // Validar que se haya subido un archivo
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return ['exito' => false, 'mensaje' => 'Error al subir el archivo'];
        }
        
        // Validar tamaño (5MB máximo)
        $tamanoMaximo = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $tamanoMaximo) {
            return ['exito' => false, 'mensaje' => 'La imagen es muy grande. Máximo 5MB'];
        }
        
        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoMime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($tipoMime, $tiposPermitidos)) {
            return ['exito' => false, 'mensaje' => 'Tipo de archivo no permitido. Solo JPG, PNG, WEBP'];
        }
        
        // Obtener extensión del archivo
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        // Generar nombre único para el archivo
        $nombreArchivo = 'plato_' . uniqid() . '_' . time() . '.' . $extension;
        $rutaCompleta = $directorioDestino . $nombreArchivo;
        
        // Mover el archivo al directorio de destino
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            // Guardar solo la ruta relativa desde assets/img/
            return ['exito' => true, 'nombre_archivo' => 'uploads/' . $nombreArchivo];
        } else {
            return ['exito' => false, 'mensaje' => 'Error al mover el archivo'];
        }
    }
    
    /**
     * Eliminar imagen física del servidor
     */
    private function eliminarImagen($nombreArchivo) {
        if (empty($nombreArchivo)) {
            return true;
        }
        
        $rutaArchivo = __DIR__ . '/../assets/img/' . $nombreArchivo;
        
        if (file_exists($rutaArchivo)) {
            return unlink($rutaArchivo);
        }
        
        return true;
    }
    
    /**
     * Crear un nuevo plato
     */
    public function crear($datos) {
        try {
            // Validaciones
            if (empty($datos['nombre']) || empty($datos['precio'])) {
                return ['exito' => false, 'mensaje' => 'Nombre y precio son obligatorios'];
            }
            
            // Validar y subir imagen
            if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                return ['exito' => false, 'mensaje' => 'Debe seleccionar una imagen'];
            }
            
            $resultadoImagen = $this->subirImagen($_FILES['imagen']);
            if (!$resultadoImagen['exito']) {
                return $resultadoImagen;
            }
            
            $nombreImagen = $resultadoImagen['nombre_archivo'];
            
            // Preparar consulta
            $sql = "INSERT INTO plato (nombre, descripcion, precio, imagen, categoria, disponible) 
                    VALUES (:nombre, :descripcion, :precio, :imagen, :categoria, :disponible)";
            
            $stmt = $this->conexion->prepare($sql);
            
            $disponible = isset($datos['disponible']) && $datos['disponible'] == 1 ? 1 : 0;
            
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':precio' => $datos['precio'],
                ':imagen' => $nombreImagen,
                ':categoria' => $datos['categoria'] ?? 'principal',
                ':disponible' => $disponible
            ]);
            
            return [
                'exito' => true, 
                'mensaje' => 'Plato creado exitosamente',
                'id_plato' => $this->conexion->lastInsertId()
            ];
            
        } catch (PDOException $e) {
            // Si falla, eliminar la imagen subida
            if (isset($nombreImagen)) {
                $this->eliminarImagen($nombreImagen);
            }
            error_log("Error al crear plato: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al crear el plato: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar un plato
     */
    public function actualizar($id, $datos) {
        try {
            // Validaciones
            if (empty($datos['nombre']) || empty($datos['precio'])) {
                return ['exito' => false, 'mensaje' => 'Nombre y precio son obligatorios'];
            }
            
            // Obtener imagen actual
            $imagenActual = $datos['imagen_actual'] ?? '';
            $nombreImagen = $imagenActual;
            
            // Si se subió una nueva imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $resultadoImagen = $this->subirImagen($_FILES['imagen']);
                
                if ($resultadoImagen['exito']) {
                    $nombreImagen = $resultadoImagen['nombre_archivo'];
                    // Eliminar imagen anterior
                    if (!empty($imagenActual)) {
                        $this->eliminarImagen($imagenActual);
                    }
                } else {
                    return $resultadoImagen;
                }
            }
            
            // Preparar consulta
            $sql = "UPDATE plato 
                    SET nombre = :nombre, 
                        descripcion = :descripcion, 
                        precio = :precio, 
                        imagen = :imagen, 
                        categoria = :categoria, 
                        disponible = :disponible 
                    WHERE id_plato = :id";
            
            $stmt = $this->conexion->prepare($sql);
            
            $disponible = isset($datos['disponible']) && $datos['disponible'] == 1 ? 1 : 0;
            
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':precio' => $datos['precio'],
                ':imagen' => $nombreImagen,
                ':categoria' => $datos['categoria'] ?? 'principal',
                ':disponible' => $disponible,
                ':id' => $id
            ]);
            
            return ['exito' => true, 'mensaje' => 'Plato actualizado exitosamente'];
            
        } catch (PDOException $e) {
            error_log("Error al actualizar plato: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al actualizar el plato: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar un plato
     */
    public function eliminar($id) {
        try {
            // Obtener información del plato antes de eliminarlo
            $sql = "SELECT imagen FROM plato WHERE id_plato = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            $plato = $stmt->fetch();
            
            if (!$plato) {
                return ['exito' => false, 'mensaje' => 'Plato no encontrado'];
            }
            
            // Eliminar de la base de datos
            $sql = "DELETE FROM plato WHERE id_plato = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            // Eliminar imagen física
            $this->eliminarImagen($plato['imagen']);
            
            return ['exito' => true, 'mensaje' => 'Plato eliminado exitosamente'];
            
        } catch (PDOException $e) {
            error_log("Error al eliminar plato: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al eliminar el plato: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cambiar disponibilidad
     */
    public function cambiarDisponibilidad($id, $disponible) {
        try {
            $sql = "UPDATE plato SET disponible = :disponible WHERE id_plato = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':disponible' => $disponible,
                ':id' => $id
            ]);
            
            $estado = $disponible ? 'disponible' : 'no disponible';
            return ['exito' => true, 'mensaje' => "Plato marcado como $estado"];
            
        } catch (PDOException $e) {
            error_log("Error al cambiar disponibilidad: " . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Error al cambiar disponibilidad: ' . $e->getMessage()];
        }
    }
    
    /**
     * Listar todos los platos
     */
    public function listarTodos($filtros = []) {
        try {
            $sql = "SELECT * FROM plato WHERE 1=1";
            $params = [];
            
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                $sql .= " AND categoria = :categoria";
                $params[':categoria'] = $filtros['categoria'];
            }
            
            if (isset($filtros['disponible'])) {
                $sql .= " AND disponible = :disponible";
                $params[':disponible'] = $filtros['disponible'];
            }
            
            $sql .= " ORDER BY fecha_creacion DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al listar platos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener un plato por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM plato WHERE id_plato = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener plato: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener platos por categoría
     */
    public function obtenerPorCategoria($categoria) {
        try {
            $sql = "SELECT * FROM plato 
                    WHERE categoria = :categoria AND disponible = 1
                    ORDER BY nombre";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':categoria' => $categoria]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener platos por categoría: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN disponible = 1 THEN 1 ELSE 0 END) as disponibles,
                        SUM(CASE WHEN categoria = 'entrada' THEN 1 ELSE 0 END) as entradas,
                        SUM(CASE WHEN categoria = 'principal' THEN 1 ELSE 0 END) as principales,
                        SUM(CASE WHEN categoria = 'postre' THEN 1 ELSE 0 END) as postres,
                        SUM(CASE WHEN categoria = 'bebida' THEN 1 ELSE 0 END) as bebidas
                    FROM plato";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Platos más populares (basado en pedidos)
     */
    public function masPopulares($limite = 10) {
        try {
            $sql = "SELECT p.*, COUNT(dp.id_detalle) as total_pedidos
                    FROM plato p
                    LEFT JOIN detalle_pedido dp ON p.id_plato = dp.plato_id
                    WHERE p.disponible = 1
                    GROUP BY p.id_plato
                    ORDER BY total_pedidos DESC
                    LIMIT :limite";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener platos populares: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar platos
     */
    public function buscar($termino) {
        try {
            $sql = "SELECT * FROM plato 
                    WHERE (nombre LIKE :termino OR descripcion LIKE :termino)
                    AND disponible = 1
                    ORDER BY nombre";
            
            $stmt = $this->conexion->prepare($sql);
            $terminoBusqueda = "%{$termino}%";
            $stmt->execute([':termino' => $terminoBusqueda]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al buscar platos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Platos más vendidos
     */
    public function masPedidos($limite = 5) {
        try {
            $sql = "SELECT p.*, COUNT(dp.plato_id) as total_pedidos
                    FROM plato p
                    INNER JOIN detalle_pedido dp ON p.id_plato = dp.plato_id
                    GROUP BY p.id_plato
                    ORDER BY total_pedidos DESC
                    LIMIT :limite";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener platos más pedidos: " . $e->getMessage());
            return [];
        }
    }
}
?>