<?php
/**
 * Controlador de Registro
 * Compatible con InfinityFree usando MD5
 */

session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que sea petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registro.php');
    exit();
}

// Obtener datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$clave = trim($_POST['clave'] ?? '');
$confirmar_clave = trim($_POST['confirmar_clave'] ?? '');

// Guardar datos del formulario en sesión por si hay error
$_SESSION['form_data'] = [
    'nombre' => $nombre,
    'correo' => $correo,
    'telefono' => $telefono
];

// Validaciones básicas
if (empty($nombre) || empty($correo) || empty($clave) || empty($confirmar_clave)) {
    $_SESSION['error'] = 'Por favor completa todos los campos obligatorios';
    header('Location: ../registro.php');
    exit();
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'El correo electrónico no es válido';
    header('Location: ../registro.php');
    exit();
}

// Validar longitud de contraseña
if (strlen($clave) < 6) {
    $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
    header('Location: ../registro.php');
    exit();
}

// Validar que las contraseñas coincidan
if ($clave !== $confirmar_clave) {
    $_SESSION['error'] = 'Las contraseñas no coinciden';
    header('Location: ../registro.php');
    exit();
}

// Validar teléfono si se proporcionó
if (!empty($telefono) && !preg_match('/^[0-9]{9,15}$/', $telefono)) {
    $_SESSION['error'] = 'El teléfono debe contener solo números (9-15 dígitos)';
    header('Location: ../registro.php');
    exit();
}

try {
    $conexion = obtenerConexion();
    
    // Verificar si el correo ya existe
    $sqlCheck = "SELECT id_usuario FROM usuario WHERE correo = :correo";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->execute([':correo' => $correo]);
    
    if ($stmtCheck->fetch()) {
        $_SESSION['error'] = 'Este correo electrónico ya está registrado';
        header('Location: ../registro.php');
        exit();
    }
    
    // Insertar nuevo usuario con MD5
    // IMPORTANTE: MD5 se usa para compatibilidad con InfinityFree
    $sql = "INSERT INTO usuario (nombre, correo, clave, telefono, rol, estado) 
            VALUES (:nombre, :correo, MD5(:clave), :telefono, 'cliente', 'activo')";
    
    $stmt = $conexion->prepare($sql);
    $resultado = $stmt->execute([
        ':nombre' => $nombre,
        ':correo' => $correo,
        ':clave' => $clave,  // MD5 se aplica en SQL
        ':telefono' => !empty($telefono) ? $telefono : NULL
    ]);
    
    if ($resultado) {
        // Registro exitoso
        unset($_SESSION['form_data']); // Limpiar datos del formulario
        $_SESSION['exito'] = '¡Registro exitoso! Ya puedes iniciar sesión con tus credenciales.';
        header('Location: ../login.php');
        exit();
        
    } else {
        $_SESSION['error'] = 'Error al registrar usuario. Intenta nuevamente.';
        header('Location: ../registro.php');
        exit();
    }
    
} catch (PDOException $e) {
    // Error de base de datos
    error_log("Error en registro: " . $e->getMessage());
    
    // Verificar si es error de correo duplicado (aunque ya lo verificamos antes)
    if ($e->getCode() == 23000) { // Código de violación de restricción única
        $_SESSION['error'] = 'Este correo electrónico ya está registrado';
    } else {
        $_SESSION['error'] = 'Error al procesar la solicitud. Intenta nuevamente.';
    }
    
    header('Location: ../registro.php');
    exit();
}
?>