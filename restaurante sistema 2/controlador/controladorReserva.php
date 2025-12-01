
<?php
/**
 * Controlador de Reservas
 * Procesa las solicitudes de reserva de mesas
 */

session_start();
require_once __DIR__ . '/../modelo/Reserva.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $personas = $_POST['personas'] ?? '';
    $mensaje = trim($_POST['mensaje'] ?? '');
    
    // Validaciones básicas
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = 'El nombre es obligatorio';
    }
    
    if (empty($telefono)) {
        $errores[] = 'El teléfono es obligatorio';
    }
    
    if (empty($correo)) {
        $errores[] = 'El correo es obligatorio';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no es válido';
    }
    
    if (empty($fecha)) {
        $errores[] = 'La fecha es obligatoria';
    } else {
        // Validar que la fecha no sea en el pasado
        $fechaReserva = strtotime($fecha);
        $hoy = strtotime(date('Y-m-d'));
        if ($fechaReserva < $hoy) {
            $errores[] = 'La fecha no puede ser en el pasado';
        }
    }
    
    if (empty($hora)) {
        $errores[] = 'La hora es obligatoria';
    }
    
    if (empty($personas) || $personas < 1) {
        $errores[] = 'Debe indicar el número de personas (mínimo 1)';
    } elseif ($personas > 20) {
        $errores[] = 'Para grupos mayores a 20 personas, contacte directamente al restaurante';
    }
    
    // Si hay errores, regresar
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        $_SESSION['form_data'] = $_POST;
        header('Location: ../vista/reservas.php');
        exit();
    }
    
    // Verificar disponibilidad
    $modeloReserva = new Reserva();
    $disponibilidad = $modeloReserva->verificarDisponibilidad($fecha, $hora);
    
    if (!$disponibilidad['disponible']) {
        $_SESSION['error'] = 'Lo sentimos, no hay disponibilidad para esa fecha y hora. Por favor seleccione otro horario.';
        $_SESSION['form_data'] = $_POST;
        header('Location: ../vista/reservas.php');
        exit();
    }
    
    // Crear la reserva
    $datos = [
        'usuario_id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $nombre,
        'telefono' => $telefono,
        'correo' => $correo,
        'fecha' => $fecha,
        'hora' => $hora,
        'personas' => $personas,
        'mensaje' => $mensaje
    ];
    
    $resultado = $modeloReserva->crear($datos);
    
    if ($resultado['exito']) {
        $_SESSION['exito'] = $resultado['mensaje'];
        unset($_SESSION['form_data']);
        header('Location: ../vista/reservas.php');
        exit();
    } else {
        $_SESSION['error'] = $resultado['mensaje'];
        $_SESSION['form_data'] = $_POST;
        header('Location: ../vista/reservas.php');
        exit();
    }
    
} else {
    header('Location: ../vista/reservas.php');
    exit();
}
?>