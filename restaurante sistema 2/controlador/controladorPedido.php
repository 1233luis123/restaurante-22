
<?php
/**
 * Controlador de Pedidos
 * Procesa la creación de pedidos de delivery
 */

session_start();
require_once __DIR__ . '/../modelo/Pedido.php';
require_once __DIR__ . '/../modelo/Plato.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['sesion_activa']) || !$_SESSION['sesion_activa']) {
    $_SESSION['error'] = 'Debes iniciar sesión para hacer pedidos';
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener datos del formulario
    $direccion_entrega = trim($_POST['direccion_entrega'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $notas = trim($_POST['notas'] ?? '');
    
    // Validaciones básicas
    $errores = [];
    
    if (empty($direccion_entrega)) {
        $errores[] = 'La dirección de entrega es obligatoria';
    } elseif (strlen($direccion_entrega) < 10) {
        $errores[] = 'La dirección debe ser más específica (mínimo 10 caracteres)';
    }
    
    if (empty($telefono)) {
        $errores[] = 'El teléfono de contacto es obligatorio';
    } elseif (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores[] = 'El teléfono debe tener 9 dígitos';
    }
    
    // Verificar que hay items en el carrito
    if (empty($_SESSION['carrito'])) {
        $errores[] = 'El carrito está vacío. Agrega productos antes de realizar el pedido';
    }
    
    // Si hay errores, regresar
    if (!empty($errores)) {
        $_SESSION['error'] = implode('<br>', $errores);
        header('Location: ../vista/pedidos.php');
        exit();
    }
    
    // Procesar el pedido
    try {
        $modeloPedido = new Pedido();
        $modeloPlato = new Plato();
        
        // Preparar detalles del pedido y calcular total
        $detalles = [];
        $total = 0;
        
        foreach ($_SESSION['carrito'] as $item) {
            // Verificar que el plato existe y está disponible
            $plato = $modeloPlato->obtenerPorId($item['id_plato']);
            
            if (!$plato || !$plato['disponible']) {
                $errores[] = "El plato '{$item['nombre']}' ya no está disponible";
                continue;
            }
            
            // Verificar que el precio no haya cambiado
            $precio_actual = $plato['precio'];
            $subtotal = $precio_actual * $item['cantidad'];
            
            $detalles[] = [
                'plato_id' => $item['id_plato'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $precio_actual,
                'subtotal' => $subtotal
            ];
            
            $total += $subtotal;
        }
        
        // Si hubo errores en la verificación de productos
        if (!empty($errores)) {
            $_SESSION['error'] = implode('<br>', $errores);
            header('Location: ../vista/pedidos.php');
            exit();
        }
        
        // Si no hay detalles válidos
        if (empty($detalles)) {
            $_SESSION['error'] = 'No hay productos válidos en el pedido';
            header('Location: ../vista/pedidos.php');
            exit();
        }
        
        // Crear el pedido
        $datosPedido = [
            'usuario_id' => $_SESSION['usuario_id'],
            'direccion_entrega' => $direccion_entrega,
            'telefono' => $telefono,
            'total' => $total,
            'notas' => $notas
        ];
        
        $resultado = $modeloPedido->crear($datosPedido, $detalles);
        
        if ($resultado['exito']) {
            // Vaciar el carrito
            $_SESSION['carrito'] = [];
            
            // Mensaje de éxito
            $_SESSION['exito'] = '¡Pedido realizado exitosamente! Tu pedido #' . $resultado['id_pedido'] . 
                                ' será entregado en 30-45 minutos aproximadamente. Te hemos enviado los detalles por correo.';
            
            // Redirigir a la página de confirmación o perfil
            header('Location: ../vista/pedidos.php?pedido_exitoso=' . $resultado['id_pedido']);
            exit();
            
        } else {
            $_SESSION['error'] = $resultado['mensaje'];
            header('Location: ../vista/pedidos.php');
            exit();
        }
        
    } catch (Exception $e) {
        error_log("Error al procesar pedido: " . $e->getMessage());
        $_SESSION['error'] = 'Ocurrió un error al procesar tu pedido. Por favor intenta nuevamente.';
        header('Location: ../vista/pedidos.php');
        exit();
    }
    
} else {
    // Si no es POST, redirigir
    header('Location: ../vista/pedidos.php');
    exit();
}
?>