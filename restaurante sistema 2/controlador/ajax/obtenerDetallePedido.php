
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require_once __DIR__ . '/../../modelo/Pedido.php';

if (!isset($_GET['id_pedido'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de pedido no proporcionado']);
    exit();
}

$modeloPedido = new Pedido();
$pedido = $modeloPedido->obtenerDetallePedido($_GET['id_pedido']);

if (!$pedido) {
    http_response_code(404);
    echo json_encode(['error' => 'Pedido no encontrado']);
    exit();
}

header('Content-Type: application/json');
echo json_encode($pedido);