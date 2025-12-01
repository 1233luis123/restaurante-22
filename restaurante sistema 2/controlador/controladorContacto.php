
<?php
/**
 * Controlador de Contacto
 * Procesa los mensajes enviados desde el formulario de contacto
 */

session_start();
require_once __DIR__ . '/../modelo/Contacto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    
    // Validaciones
    $errores = [];
    
    // Validar nombre
    if (empty($nombre)) {
        $errores[] = 'El nombre es obligatorio';
    } elseif (strlen($nombre) < 3) {
        $errores[] = 'El nombre debe tener al menos 3 caracteres';
    } elseif (strlen($nombre) > 100) {
        $errores[] = 'El nombre no puede exceder 100 caracteres';
    } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
        $errores[] = 'El nombre solo puede contener letras y espacios';
    }
    
    // Validar correo
    if (empty($correo)) {
        $errores[] = 'El correo electrónico es obligatorio';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido';
    } elseif (strlen($correo) > 100) {
        $errores[] = 'El correo no puede exceder 100 caracteres';
    }
    
    // Validar teléfono (opcional pero si se ingresa debe ser válido)
    if (!empty($telefono)) {
        if (!preg_match('/^[0-9]{9}$/', $telefono)) {
            $errores[] = 'El teléfono debe tener exactamente 9 dígitos';
        }
    }
    
    // Validar mensaje
    if (empty($mensaje)) {
        $errores[] = 'El mensaje es obligatorio';
    } elseif (strlen($mensaje) < 10) {
        $errores[] = 'El mensaje debe tener al menos 10 caracteres';
    } elseif (strlen($mensaje) > 1000) {
        $errores[] = 'El mensaje no puede exceder 1000 caracteres';
    }
    
    // Validación anti-spam básica
    if (!empty($mensaje)) {
        // Detectar URLs sospechosas
        $patron_url = '/(http|https|www\.|\.com|\.net|\.org)/i';
        if (preg_match($patron_url, $mensaje)) {
            $errores[] = 'El mensaje no puede contener enlaces web';
        }
        
        // Detectar repetición excesiva de caracteres
        if (preg_match('/(.)\1{10,}/', $mensaje)) {
            $errores[] = 'El mensaje contiene caracteres repetitivos sospechosos';
        }
    }
    
    // Verificar tiempo entre mensajes (protección contra spam)
    if (isset($_SESSION['ultimo_mensaje_tiempo'])) {
        $tiempo_transcurrido = time() - $_SESSION['ultimo_mensaje_tiempo'];
        if ($tiempo_transcurrido < 60) { // 1 minuto entre mensajes
            $segundos_restantes = 60 - $tiempo_transcurrido;
            $errores[] = "Por favor espera {$segundos_restantes} segundos antes de enviar otro mensaje";
        }
    }
    
    // Si hay errores, regresar
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        $_SESSION['form_data'] = $_POST;
        header('Location: ../vista/contacto.php');
        exit();
    }
    
    // Intentar guardar el mensaje
    try {
        $modeloContacto = new Contacto();
        
        $datos = [
            'nombre' => $nombre,
            'correo' => $correo,
            'telefono' => !empty($telefono) ? $telefono : null,
            'mensaje' => $mensaje
        ];
        
        $resultado = $modeloContacto->crear($datos);
        
        if ($resultado['exito']) {
            // Registrar el tiempo del mensaje para control de spam
            $_SESSION['ultimo_mensaje_tiempo'] = time();
            
            // Mensaje de éxito
            $_SESSION['exito'] = $resultado['mensaje'];
            
            // Limpiar datos del formulario
            unset($_SESSION['form_data']);
            
            // Aquí podrías enviar un email de notificación al administrador
            // enviarEmailNotificacion($datos);
            
            // También podrías enviar un email de confirmación al usuario
            // enviarEmailConfirmacion($correo, $nombre);
            
            header('Location: ../vista/contacto.php');
            exit();
            
        } else {
            $_SESSION['error'] = $resultado['mensaje'];
            $_SESSION['form_data'] = $_POST;
            header('Location: ../vista/contacto.php');
            exit();
        }
        
    } catch (Exception $e) {
        error_log("Error al procesar mensaje de contacto: " . $e->getMessage());
        $_SESSION['error'] = 'Ocurrió un error al enviar tu mensaje. Por favor intenta nuevamente más tarde.';
        $_SESSION['form_data'] = $_POST;
        header('Location: ../vista/contacto.php');
        exit();
    }
    
} else {
    // Si no es POST, redirigir
    header('Location: ../vista/contacto.php');
    exit();
}

/**
 * Función auxiliar para enviar email de notificación al administrador
 * (Descomenta y configura según tus necesidades)
 */
/*
function enviarEmailNotificacion($datos) {
    $to = "admin@chocos.com";
    $subject = "Nuevo mensaje de contacto - Choco's Restaurante";
    $message = "
        Nuevo mensaje recibido:\n\n
        Nombre: {$datos['nombre']}\n
        Correo: {$datos['correo']}\n
        Teléfono: {$datos['telefono']}\n
        Mensaje:\n{$datos['mensaje']}
    ";
    $headers = "From: noreply@chocos.com";
    
    mail($to, $subject, $message, $headers);
}
*/

/**
 * Función auxiliar para enviar email de confirmación al usuario
 * (Descomenta y configura según tus necesidades)
 */
/*
function enviarEmailConfirmacion($correo, $nombre) {
    $to = $correo;
    $subject = "Hemos recibido tu mensaje - Choco's Restaurante";
    $message = "
        Hola {$nombre},\n\n
        Hemos recibido tu mensaje y te responderemos a la brevedad posible.\n\n
        Gracias por contactarnos.\n\n
        Atentamente,\n
        Equipo Choco's Restaurante
    ";
    $headers = "From: noreply@chocos.com";
    
    mail($to, $subject, $message, $headers);
}
*/
?>