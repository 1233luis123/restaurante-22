<?php
/**
 * Controlador de Login
 * Compatible con InfinityFree usando MD5
 */

session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que sea petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// Obtener datos del formulario
$correo = trim($_POST['correo'] ?? '');
$clave = trim($_POST['clave'] ?? '');

// Validaciones básicas
if (empty($correo) || empty($clave)) {
    $_SESSION['error'] = 'Por favor completa todos los campos';
    header('Location: ../login.php');
    exit();
}

try {
    $conexion = obtenerConexion();
    
    // Buscar usuario por correo y verificar contraseña con MD5
    // IMPORTANTE: MD5 se usa para compatibilidad con InfinityFree
    $sql = "SELECT * FROM usuario 
            WHERE correo = :correo 
            AND clave = MD5(:clave) 
            AND estado = 'activo'";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':correo' => $correo,
        ':clave' => $clave
    ]);
    
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        // Login exitoso - Crear sesión
        $_SESSION['sesion_activa'] = true;
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_correo'] = $usuario['correo'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['usuario_telefono'] = $usuario['telefono'] ?? '';
        
        // Registrar último acceso (opcional)
        try {
            $sqlUpdate = "UPDATE usuario SET fecha_registro = NOW() WHERE id_usuario = :id";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->execute([':id' => $usuario['id_usuario']]);
        } catch (Exception $e) {
            // No hacer nada si falla, es opcional
        }
        
        // Redirigir según el rol
        if ($usuario['rol'] === 'admin') {
            header('Location: ../vista/admin/dashboard.php');
        } else {
            // Cliente normal
            $_SESSION['exito'] = '¡Bienvenido de nuevo, ' . $usuario['nombre'] . '!';
            header('Location: ../index.php');
        }
        exit();
        
    } else {
        // Login fallido
        $_SESSION['error'] = 'Correo o contraseña incorrectos';
        header('Location: ../login.php');
        exit();
    }
    
} catch (PDOException $e) {
    // Error de base de datos
    error_log("Error en login: " . $e->getMessage());
    $_SESSION['error'] = 'Error al procesar la solicitud. Intenta nuevamente.';
    header('Location: ../login.php');
    exit();
}
?>