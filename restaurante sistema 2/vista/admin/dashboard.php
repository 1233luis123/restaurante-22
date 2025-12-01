
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../modelo/Usuario.php';
require_once __DIR__ . '/../../modelo/Plato.php';
require_once __DIR__ . '/../../modelo/Reserva.php';
require_once __DIR__ . '/../../modelo/Pedido.php';

// Obtener estadísticas
$modeloUsuario = new Usuario();
$modeloPlato = new Plato();
$modeloReserva = new Reserva();
$modeloPedido = new Pedido();

$totalUsuarios = count($modeloUsuario->listarTodos());
$estadisticasPlatos = $modeloPlato->obtenerEstadisticas();
$estadisticasReservas = $modeloReserva->obtenerEstadisticas();
$estadisticasPedidos = $modeloPedido->obtenerEstadisticas();

$reservasHoy = $modeloReserva->reservasHoy();
$proximasReservas = $modeloReserva->proximasReservas(5);
$pedidosRecientes = $modeloPedido->obtenerRecientes(5);
$platosMasPedidos = $modeloPlato->masPedidos(5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Choco's Restaurante</title>
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
        .admin-header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        .stat-icon.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-icon.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .stat-icon.warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
        }
        .stat-icon.danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #2d3748;
        }
        .stat-label {
            color: #718096;
            font-weight: 600;
            margin-top: 5px;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .table-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
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
                <a href="dashboard.php" class="active">
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
                <a href="mensajes.php">
                    <i class="fas fa-envelope"></i>
                    Mensajes
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
        <div class="admin-header">
            <div>
                <h1 class="h3 mb-0">Dashboard Administrativo</h1>
                <p class="text-muted mb-0">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
            </div>
            <div>
                <span class="badge bg-success">
                    <i class="fas fa-circle me-1"></i> Sistema Activo
                </span>
            </div>
        </div>
        
        <!-- Tarjetas de Estadísticas -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo $totalUsuarios; ?></div>
                    <div class="stat-label">Total Usuarios</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-value"><?php echo $estadisticasPlatos['disponibles'] ?? 0; ?></div>
                    <div class="stat-label">Platos Disponibles</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value"><?php echo $estadisticasReservas['pendientes'] ?? 0; ?></div>
                    <div class="stat-label">Reservas Pendientes</div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon danger">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-value"><?php echo $estadisticasPedidos['pendientes'] ?? 0; ?></div>
                    <div class="stat-label">Pedidos Activos</div>
                </div>
            </div>
        </div>
        
        <!-- Gráficas y Tablas -->
        <div class="row">
            <!-- Reservas de Hoy -->
            <div class="col-lg-6 mb-4">
                <div class="table-container">
                    <div class="table-title">
                        <i class="fas fa-calendar-day text-primary"></i>
                        Reservas de Hoy
                    </div>
                    
                    <?php if (empty($reservasHoy)): ?>
                        <p class="text-muted text-center py-4">No hay reservas para hoy</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Cliente</th>
                                        <th>Personas</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservasHoy as $reserva): ?>
                                    <tr>
                                        <td><strong><?php echo date('H:i', strtotime($reserva['hora'])); ?></strong></td>
                                        <td><?php echo htmlspecialchars($reserva['nombre']); ?></td>
                                        <td><i class="fas fa-user me-1"></i><?php echo $reserva['personas']; ?></td>
                                        <td>
                                            <span class="badge badge-estado <?php echo $reserva['estado'] === 'confirmada' ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo ucfirst($reserva['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-end mt-3">
                        <a href="gestionReservas.php" class="btn btn-sm btn-outline-primary">
                            Ver Todas <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Pedidos Recientes -->
            <div class="col-lg-6 mb-4">
                <div class="table-container">
                    <div class="table-title">
                        <i class="fas fa-shopping-cart text-success"></i>
                        Pedidos Recientes
                    </div>
                    
                    <?php if (empty($pedidosRecientes)): ?>
                        <p class="text-muted text-center py-4">No hay pedidos recientes</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidosRecientes as $pedido): ?>
                                    <tr>
                                        <td><strong>#<?php echo $pedido['id_pedido']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($pedido['usuario_nombre'] ?? 'N/A'); ?></td>
                                        <td><strong>S/ <?php echo number_format($pedido['total'], 2); ?></strong></td>
                                        <td>
                                            <?php
                                            $claseEstado = [
                                                'pendiente' => 'bg-warning',
                                                'preparando' => 'bg-info',
                                                'enviado' => 'bg-primary',
                                                'entregado' => 'bg-success',
                                                'cancelado' => 'bg-danger'
                                            ];
                                            $clase = $claseEstado[$pedido['estado']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge badge-estado <?php echo $clase; ?>">
                                                <?php echo ucfirst($pedido['estado']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-end mt-3">
                        <a href="gestionPedidos.php" class="btn btn-sm btn-outline-success">
                            Ver Todos <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Platos Más Pedidos -->
        <div class="row">
            <div class="col-12">
                <div class="table-container">
                    <div class="table-title">
                        <i class="fas fa-star text-warning"></i>
                        Platos Más Pedidos
                    </div>
                    
                    <?php if (empty($platosMasPedidos)): ?>
                        <p class="text-muted text-center py-4">No hay datos de platos pedidos</p>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($platosMasPedidos as $plato): ?>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($plato['nombre']); ?></h6>
                                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">
                                                <i class="fas fa-shopping-cart me-1"></i>
                                                <?php echo $plato['total_pedidos']; ?> pedidos
                                            </span>
                                            <strong class="text-danger">S/ <?php echo number_format($plato['precio'], 2); ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>