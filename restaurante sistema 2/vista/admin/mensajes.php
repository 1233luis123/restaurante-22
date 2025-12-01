
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../modelo/Contacto.php';

$modeloContacto = new Contacto();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'marcar_leido':
                $resultado = $modeloContacto->marcarLeido($_POST['id_contacto']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
                
            case 'eliminar':
                $resultado = $modeloContacto->eliminar($_POST['id_contacto']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
        }
    }
}

$mensajes = $modeloContacto->listarTodos();
$estadisticas = $modeloContacto->obtenerEstadisticas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - Choco's Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding: 20px;
            z-index: 1000;
        }
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #dc3545;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(220, 53, 69, 0.2);
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        .mensaje-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #dc3545;
        }
        .mensaje-card.leido {
            opacity: 0.7;
            border-left-color: #6c757d;
        }
        .mensaje-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-utensils"></i>
            Choco's Admin
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="gestionPlatos.php">
                    <i class="fas fa-utensils"></i>
                    Gestión de Platos
                </a>
            </li>
            <li>
                <a href="gestionPedidos.php">
                    <i class="fas fa-shopping-cart"></i>
                    Gestión de Pedidos
                </a>
            </li>
            <li>
                <a href="gestionReservas.php">
                    <i class="fas fa-calendar-alt"></i>
                    Gestión de Reservas
                </a>
            </li>
            <li>
                <a href="gestionUsuarios.php">
                    <i class="fas fa-users"></i>
                    Gestión de Usuarios
                </a>
            </li>
            <li>
                <a href="mensajes.php" class="active">
                    <i class="fas fa-envelope"></i>
                    Mensajes
                    <?php if ($estadisticas['no_leidos'] > 0): ?>
                        <span class="badge bg-danger"><?php echo $estadisticas['no_leidos']; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="reportes.php">
                    <i class="fas fa-file-alt"></i>
                    Reportes
                </a>
            </li>
            <li><hr style="border-color: rgba(255,255,255,0.1);"></li>
            <li>
                <a href="../../index.php">
                    <i class="fas fa-home"></i>
                    Ir al Sitio Web
                </a>
            </li>
            <li>
                <a href="../../logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Contenido Principal -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="mb-4">
            <h1 class="h3 mb-0">Mensajes de Contacto</h1>
            <p class="text-muted mb-0">Administra los mensajes recibidos</p>
        </div>
        
        <!-- Mensajes -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Estadísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['total'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-envelope me-2"></i>Total Mensajes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['no_leidos'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-envelope-open me-2"></i>No Leídos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['leidos'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-check-double me-2"></i>Leídos</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Listado de Mensajes -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Todos los Mensajes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($mensajes)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay mensajes registrados
                    </div>
                <?php else: ?>
                    <?php foreach ($mensajes as $msg): ?>
                        <div class="mensaje-card <?php echo $msg['leido'] ? 'leido' : ''; ?>">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-1">
                                                <?php if (!$msg['leido']): ?>
                                                    <span class="badge bg-danger me-2">Nuevo</span>
                                                <?php endif; ?>
                                                <i class="fas fa-user-circle text-danger me-2"></i>
                                                <?php echo htmlspecialchars($msg['nombre']); ?>
                                            </h5>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-envelope me-2"></i>
                                                <?php echo htmlspecialchars($msg['correo']); ?>
                                            </p>
                                            <?php if (!empty($msg['telefono'])): ?>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-phone me-2"></i>
                                                <?php echo htmlspecialchars($msg['telefono']); ?>
                                            </p>
                                            <?php endif; ?>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-clock me-2"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($msg['fecha_envio'])); ?>
                                            </p>
                                            <div class="border-start border-3 border-danger ps-3 mt-3">
                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($msg['mensaje'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-end">
                                        <?php if (!$msg['leido']): ?>
                                        <form method="POST" class="d-inline mb-2">
                                            <input type="hidden" name="accion" value="marcar_leido">
                                            <input type="hidden" name="id_contacto" value="<?php echo $msg['id_contacto']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success w-100 mb-2">
                                                <i class="fas fa-check me-2"></i>Marcar como Leído
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        
                                        <a href="mailto:<?php echo htmlspecialchars($msg['correo']); ?>" 
                                           class="btn btn-sm btn-outline-primary w-100 mb-2">
                                            <i class="fas fa-reply me-2"></i>Responder
                                        </a>
                                        
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('¿Eliminar este mensaje?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id_contacto" value="<?php echo $msg['id_contacto']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="fas fa-trash me-2"></i>Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>