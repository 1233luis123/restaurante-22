
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../modelo/Pedido.php';
require_once __DIR__ . '/../../modelo/Reserva.php';
require_once __DIR__ . '/../../modelo/Plato.php';
require_once __DIR__ . '/../../modelo/Usuario.php';

$modeloPedido = new Pedido();
$modeloReserva = new Reserva();
$modeloPlato = new Plato();
$modeloUsuario = new Usuario();

// Obtener período de reporte
$periodo = $_GET['periodo'] ?? 'mes';

// Obtener estadísticas generales
$estadisticasPedidos = $modeloPedido->obtenerEstadisticas($periodo);
$estadisticasReservas = $modeloReserva->obtenerEstadisticas();
$totalUsuarios = count($modeloUsuario->listarTodos());
$platosMasVendidos = $modeloPedido->platosMasVendidos(10);
$ventasPorDia = $modeloPedido->ventasPorDia(7);
$totalIngresos = $modeloPedido->obtenerTotalIngresos($periodo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Choco's Restaurante</title>
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
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
        }
        @media print {
            .sidebar, .no-print {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
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
                <a href="mensajes.php">
                    <i class="fas fa-envelope"></i>
                    Mensajes
                </a>
            </li>
            <li>
                <a href="reportes.php" class="active">
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
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h1 class="h3 mb-0">Reportes y Estadísticas</h1>
                <p class="text-muted mb-0">Análisis del rendimiento del restaurante</p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-danger">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
        
        <!-- Selector de Período -->
        <div class="card mb-4 no-print">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Período de Reporte</label>
                        <select name="periodo" class="form-select" onchange="this.form.submit()">
                            <option value="dia" <?php echo $periodo === 'dia' ? 'selected' : ''; ?>>Hoy</option>
                            <option value="semana" <?php echo $periodo === 'semana' ? 'selected' : ''; ?>>Esta Semana</option>
                            <option value="mes" <?php echo $periodo === 'mes' ? 'selected' : ''; ?>>Este Mes</option>
                            <option value="anio" <?php echo $periodo === 'anio' ? 'selected' : ''; ?>>Este Año</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Resumen General -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="text-white">
                        <h3 class="text-white">S/ <?php echo number_format($totalIngresos, 2); ?></h3>
                        <p class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Ingresos Totales</p>
                        <small>Período: <?php echo ucfirst($periodo); ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="text-white">
                        <h3 class="text-white"><?php echo $estadisticasPedidos['total'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Total Pedidos</p>
                        <small>Entregados: <?php echo $estadisticasPedidos['entregados'] ?? 0; ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="text-white">
                        <h3 class="text-white"><?php echo $estadisticasReservas['total'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Reservas</p>
                        <small>Confirmadas: <?php echo $estadisticasReservas['confirmadas'] ?? 0; ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="text-white">
                        <h3 class="text-white"><?php echo $totalUsuarios; ?></h3>
                        <p class="mb-0"><i class="fas fa-users me-2"></i>Usuarios</p>
                        <small>Registrados en el sistema</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráfico de Ventas por Día -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Ventas de los Últimos 7 Días</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($ventasPorDia)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Pedidos</th>
                                        <th>Total Vendido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ventasPorDia as $venta): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($venta['fecha'])); ?></td>
                                        <td><span class="badge bg-primary"><?php echo $venta['total_pedidos']; ?></span></td>
                                        <td><strong class="text-success">S/ <?php echo number_format($venta['total_ventas'], 2); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center">No hay datos de ventas para este período</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Estado de Pedidos</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pendientes
                                <span class="badge bg-warning"><?php echo $estadisticasPedidos['pendientes'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                En Preparación
                                <span class="badge bg-info"><?php echo $estadisticasPedidos['preparando'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Enviados
                                <span class="badge bg-primary"><?php echo $estadisticasPedidos['enviados'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Entregados
                                <span class="badge bg-success"><?php echo $estadisticasPedidos['entregados'] ?? 0; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Cancelados
                                <span class="badge bg-danger"><?php echo $estadisticasPedidos['cancelados'] ?? 0; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Platos Más Vendidos -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Platos Más Vendidos</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($platosMasVendidos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ranking</th>
                                <th>Plato</th>
                                <th>Categoría</th>
                                <th>Unidades Vendidas</th>
                                <th>Ingresos Generados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $ranking = 1; ?>
                            <?php foreach ($platosMasVendidos as $plato): ?>
                            <tr>
                                <td>
                                    <?php if ($ranking <= 3): ?>
                                        <i class="fas fa-medal text-<?php echo $ranking == 1 ? 'warning' : ($ranking == 2 ? 'secondary' : 'danger'); ?> fa-2x"></i>
                                    <?php else: ?>
                                        <strong>#<?php echo $ranking; ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($plato['nombre']); ?></strong></td>
                                <td><span class="badge bg-primary"><?php echo ucfirst($plato['categoria']); ?></span></td>
                                <td><strong class="text-primary"><?php echo $plato['total_vendido']; ?></strong> unidades</td>
                                <td><strong class="text-success">S/ <?php echo number_format($plato['ingresos_generados'], 2); ?></strong></td>
                            </tr>
                            <?php $ranking++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No hay datos de ventas disponibles</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Información del Reporte -->
        <div class="card">
            <div class="card-body text-center text-muted">
                <p class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Reporte generado el <?php echo date('d/m/Y H:i:s'); ?>
                </p>
                <p class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Administrador: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                </p>
            </div>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>