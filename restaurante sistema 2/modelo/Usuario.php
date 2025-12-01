<?php
/**
 * Modelo Usuario
 * Gestiona todas las operaciones relacionadas con usuarios
 */

require_once __DIR__ . '/../config/conexion.php';

class Usuario {
    private $conexion;
    
    public function __construct() {
        $this->conexion = obtenerConexion();
    }
    
    /**
     * Registrar nuevo usuario
     */
    public function registrar($datos) {
        try {
            // Validar que el correo no exista
            if ($this->existeCorreo($datos['correo'])) {
                return [
                    'exito' => false,
                    'mensaje' => 'El correo electrónico ya está registrado'
                ];
            }
            
            // Usar PASSWORD() de MySQL para encriptar
            $sql = "INSERT INTO usuario (nombre, correo, clave, telefono, rol) 
                    VALUES (:nombre, :correo, PASSWORD(:clave), :telefono, 'cliente')";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':correo' => $datos['correo'],
                ':clave' => $datos['clave'],
                ':telefono' => $datos['telefono'] ?? null
            ]);
            
            return [
                'exito' => true,
                'mensaje' => 'Usuario registrado exitosamente',
                'id_usuario' => $this->conexion->lastInsertId()
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al registrar usuario: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Iniciar sesión (usando PASSWORD de MySQL)
     */
    public function login($correo, $clave) {
        try {
            // Verificar usuario y contraseña con PASSWORD()
            $sql = "SELECT u.*, (u.clave = PASSWORD(:clave)) as clave_correcta 
                    FROM usuario u 
                    WHERE u.correo = :correo AND u.estado = 'activo'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':correo' => $correo,
                ':clave' => $clave
            ]);
            
            $usuario = $stmt->fetch();
            
            if ($usuario && $usuario['clave_correcta'] == 1) {
                // No incluir la contraseña en la sesión
                unset($usuario['clave']);
                unset($usuario['clave_correcta']);
                
                return [
                    'exito' => true,
                    'mensaje' => 'Inicio de sesión exitoso',
                    'usuario' => $usuario
                ];
            } else {
                return [
                    'exito' => false,
                    'mensaje' => 'Correo o contraseña incorrectos'
                ];
            }
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al iniciar sesión: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verificar si existe un correo
     */
    private function existeCorreo($correo) {
        $sql = "SELECT COUNT(*) as total FROM usuario WHERE correo = :correo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $resultado = $stmt->fetch();
        return $resultado['total'] > 0;
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT id_usuario, nombre, correo, telefono, rol, estado, fecha_registro 
                    FROM usuario WHERE id_usuario = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Actualizar perfil de usuario
     */
    public function actualizarPerfil($id, $datos) {
        try {
            $sql = "UPDATE usuario 
                    SET nombre = :nombre, 
                        telefono = :telefono 
                    WHERE id_usuario = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':telefono' => $datos['telefono'],
                ':id' => $id
            ]);
            
            return [
                'exito' => true,
                'mensaje' => 'Perfil actualizado exitosamente'
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar perfil: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarClave($id, $claveActual, $claveNueva) {
        try {
            // Verificar contraseña actual usando PASSWORD()
            $sql = "SELECT (clave = PASSWORD(:claveActual)) as es_correcta 
                    FROM usuario WHERE id_usuario = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':claveActual' => $claveActual,
                ':id' => $id
            ]);
            $resultado = $stmt->fetch();
            
            if (!$resultado || $resultado['es_correcta'] != 1) {
                return [
                    'exito' => false,
                    'mensaje' => 'La contraseña actual es incorrecta'
                ];
            }
            
            // Actualizar contraseña con PASSWORD()
            $sql = "UPDATE usuario SET clave = PASSWORD(:clave) WHERE id_usuario = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':clave' => $claveNueva,
                ':id' => $id
            ]);
            
            return [
                'exito' => true,
                'mensaje' => 'Contraseña actualizada exitosamente'
            ];
            
        } catch (PDOException $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al cambiar contraseña: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Listar todos los usuarios (solo admin)
     */
    public function listarTodos() {
        try {
            $sql = "SELECT id_usuario, nombre, correo, telefono, rol, estado, fecha_registro 
                    FROM usuario ORDER BY fecha_registro DESC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>