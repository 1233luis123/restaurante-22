<?php
/**
 * Clase de Conexión a la Base de Datos
 * Compatible con XAMPP Local e InfinityFree
 * Patrón Singleton
 */

class Conexion {
    private static $instancia = null;
    private $conexion;
    
    // ========================================
    // CONFIGURACIÓN AUTOMÁTICA DE ENTORNO
    // ========================================
    
    /**
     * Constructor privado para Singleton
     * Detecta automáticamente si está en local o producción
     */
    private function __construct() {
        try {
            // Detectar entorno
            $esLocal = $this->esEntornoLocal();
            
            if ($esLocal) {
                // CONFIGURACIÓN PARA XAMPP LOCAL
                $config = [
                    'host' => 'localhost',
                    'puerto' => '3308',  // Cambia a 3306 si tu XAMPP usa el puerto estándar
                    'usuario' => 'root',
                    'password' => '',
                    'base_datos' => 'restaurante_db',
                    'charset' => 'utf8mb4'
                ];
            } else {
                // CONFIGURACIÓN PARA INFINITYFREE (PRODUCCIÓN)
                // ⚠️ IMPORTANTE: Reemplaza estos valores con los de tu panel de InfinityFree
                $config = [
                    'host' => 'sql200.infinityfree.com',  // Cambia por tu host
                    'puerto' => '3306',
                    'usuario' => 'epiz_XXXXXXXX',         // Tu usuario de InfinityFree
                    'password' => 'tu_password_aqui',     // Tu contraseña de BD
                    'base_datos' => 'epiz_XXXXXXXX_restaurante', // Tu nombre de BD
                    'charset' => 'utf8mb4'
                ];
            }
            
            // Crear DSN (Data Source Name)
            $dsn = "mysql:host={$config['host']};port={$config['puerto']};dbname={$config['base_datos']};charset={$config['charset']}";
            
            // Opciones de PDO
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}"
            ];
            
            // Crear conexión
            $this->conexion = new PDO($dsn, $config['usuario'], $config['password'], $opciones);
            
        } catch (PDOException $e) {
            // En producción NO mostrar detalles del error
            if ($this->esEntornoLocal()) {
                die("Error de conexión: " . $e->getMessage());
            } else {
                error_log("Error de conexión BD: " . $e->getMessage());
                die("Error al conectar con la base de datos. Por favor, contacta al administrador.");
            }
        }
    }
    
    /**
     * Detectar si estamos en entorno local o producción
     * @return bool
     */
    private function esEntornoLocal() {
        // Métodos para detectar entorno local
        $esLocal = false;
        
        // Método 1: Verificar hostname
        $hostname = gethostname();
        if (strpos($hostname, 'localhost') !== false || 
            strpos($hostname, '127.0.0.1') !== false ||
            strpos($hostname, '::1') !== false) {
            $esLocal = true;
        }
        
        // Método 2: Verificar IP del servidor
        if (isset($_SERVER['SERVER_ADDR'])) {
            $serverIp = $_SERVER['SERVER_ADDR'];
            if ($serverIp === '127.0.0.1' || $serverIp === '::1' || 
                strpos($serverIp, '192.168.') === 0 || 
                strpos($serverIp, '10.') === 0) {
                $esLocal = true;
            }
        }
        
        // Método 3: Verificar HTTP_HOST
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
            if (strpos($host, 'localhost') !== false || 
                strpos($host, '127.0.0.1') !== false ||
                strpos($host, '.test') !== false ||
                strpos($host, '.local') !== false) {
                $esLocal = true;
            }
        }
        
        // Método 4: Variable de entorno personalizada (opcional)
        if (getenv('APP_ENV') === 'local') {
            $esLocal = true;
        }
        
        return $esLocal;
    }
    
    /**
     * Obtener la única instancia de la conexión
     * @return Conexion
     */
    public static function obtenerInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }
    
    /**
     * Obtener la conexión PDO
     * @return PDO
     */
    public function obtenerConexion() {
        return $this->conexion;
    }
    
    /**
     * Prevenir clonación del objeto
     */
    private function __clone() {}
    
    /**
     * Prevenir deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton.");
    }
}

/**
 * Función helper para obtener la conexión fácilmente
 * @return PDO
 */
function obtenerConexion() {
    return Conexion::obtenerInstancia()->obtenerConexion();
}
?>