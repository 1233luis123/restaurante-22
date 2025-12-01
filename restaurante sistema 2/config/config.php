
<?php
/**
 * Archivo de Configuración General del Sistema
 */

// Zona horaria
date_default_timezone_set('America/Lima');

// Configuración de errores (cambiar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS

// Constantes del sistema
define('NOMBRE_SITIO', "Choco's Restaurante");
define('URL_BASE', 'http://localhost/chocos_restaurante/');
define('RUTA_BASE', __DIR__ . '/../');

// Constantes de la base de datos (por si necesitas usarlas)
define('DB_HOST', 'localhost');
define('DB_PORT', '3308');
define('DB_NAME', 'restaurante_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Rutas de directorios
define('RUTA_IMAGENES', RUTA_BASE . 'assets/img/');
define('URL_IMAGENES', URL_BASE . 'assets/img/');

// Configuración de uploads
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

/**
 * Función para verificar si hay sesión activa
 */
function verificarSesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true;
}

/**
 * Función para verificar si el usuario es administrador
 */
function esAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

/**
 * Función para redirigir con mensaje
 */
function redirigirCon($url, $mensaje, $tipo = 'error') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION[$tipo] = $mensaje;
    header("Location: $url");
    exit();
}

/**
 * Función para limpiar datos de entrada
 */
function limpiarDato($dato) {
    return htmlspecialchars(strip_tags(trim($dato)));
}

/**
 * Función para formatear fecha
 */
function formatearFecha($fecha, $formato = 'd/m/Y') {
    return date($formato, strtotime($fecha));
}

/**
 * Función para formatear precio
 */
function formatearPrecio($precio) {
    return 'S/ ' . number_format($precio, 2);
}
?>