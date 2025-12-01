
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../modelo/Reserva.php';

$modeloReserva = new Reserva();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'actualizar_estado':
                $resultado = $modeloReserva->actualizarEstado($_POST['id_reserva'], $_POST['estado']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
                
            case 'cancelar':
                $resultado = $modeloReserva->cancelar($_POST['id_reserva']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
        }
    }
}

// Obtener filtros
$filtroEstado = $_GET['estado'] ?? '';
$filtroFecha = $_GET['fecha'] ?? '';
$filtros = [];
if (!empty($filtroEstado)) {
    $filtros['estado'] = $filtroEstado;
}
if (!empty($filtroFecha)) {
    $filtros['fecha'] = $filtroFecha;
}

$reservas = $modeloReserva->listarTodas($filtros);
$estadisticas = $modeloReserva->obtenerEstadisticas();
$reservasHoy = $modeloReserva->reservasHoy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - Choco's Restaurante</title>
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
        .reserva-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .reserva-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
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
                <a href="gestionReservas.php" class="active">
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
        <div class="mb-4">
            <h1 class="h3 mb-0">Gestión de Reservas</h1>
            <p class="text-muted mb-0">Administra las reservas de mesas</p>
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
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['total'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-calendar me-2"></i>Total Mes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['pendientes'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-clock me-2"></i>Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['confirmadas'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-check-circle me-2"></i>Confirmadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo count($reservasHoy); ?></h3>
                        <p class="mb-0"><i class="fas fa-calendar-day me-2"></i>Hoy</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendiente" <?php echo $filtroEstado === 'pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                            <option value="confirmada" <?php echo $filtroEstado === 'confirmada' ? 'selected' : ''; ?>>Confirmadas</option>
                            <option value="cancelada" <?php echo $filtroEstado === 'cancelada' ? 'selected' : ''; ?>>Canceladas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($filtroFecha); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                        <a href="gestionReservas.php" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Reservas de Hoy -->
        <?php if (!empty($reservasHoy)): ?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Reservas de Hoy</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Personas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservasHoy as $reserva): ?>
                            <tr>
                                <td><strong><?php echo date('H:i', strtotime($reserva['hora'])); ?></strong></td>
                                <td><?php echo htmlspecialchars($reserva['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['telefono']); ?></td>
                                <td><i class="fas fa-user me-1"></i><?php echo $reserva['personas']; ?></td>
                                <td>
                                    <span class="badge <?php echo $reserva['estado'] === 'confirmada' ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo ucfirst($reserva['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($reserva['estado'] === 'pendiente'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="accion" value="actualizar_estado">
                                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                        <input type="hidden" name="estado" value="confirmada">
                                        <button type="submit" class="btn btn-sm btn-success" title="Confirmar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Listado de Reservas -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Todas las Reservas</h5>
            </div>
            <div class="card-body">
                <?php if (empty($reservas)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay reservas registradas
                    </div>
                <?php else: ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <div class="reserva-card">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-1">
                                                <i class="fas fa-user-circle text-danger me-2"></i>
                                                <?php echo htmlspecialchars($reserva['nombre']); ?>
                                            </h5>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-envelope me-2"></i>
                                                <?php echo htmlspecialchars($reserva['correo']); ?>
                                            </p>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-phone me-2"></i>
                                                <?php echo htmlspecialchars($reserva['telefono']); ?>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-calendar me-2"></i>
                                                <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($reserva['fecha'])); ?>
                                                <i class="fas fa-clock ms-3 me-2"></i>
                                                <strong>Hora:</strong> <?php echo date('H:i', strtotime($reserva['hora'])); ?>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-users me-2"></i>
                                                <strong>Personas:</strong> <?php echo $reserva['personas']; ?>
                                            </p>
                                            <?php if (!empty($reserva['mensaje'])): ?>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-comment me-2"></i>
                                                <strong>Mensaje:</strong> <?php echo htmlspecialchars($reserva['mensaje']); ?>
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <?php
                                            $claseEstado = [
                                                'pendiente' => 'bg-warning',
                                                'confirmada' => 'bg-success',
                                                'cancelada' => 'bg-danger'
                                            ];
                                            $clase = $claseEstado[$reserva['estado']] ?? 'bg-secondary';
                                            ?>
                                            <span class="badge badge-estado <?php echo $clase; ?>">
                                                <?php echo ucfirst($reserva['estado']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-end">
                                        <?php if ($reserva['estado'] === 'pendiente'): ?>
                                        <form method="POST" class="d-inline mb-2">
                                            <input type="hidden" name="accion" value="actualizar_estado">
                                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                            <input type="hidden" name="estado" value="confirmada">
                                            <button type="submit" class="btn btn-sm btn-success w-100 mb-2">
                                                <i class="fas fa-check me-2"></i>Confirmar
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($reserva['estado'] !== 'cancelada'): ?>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('¿Cancelar esta reserva?')">
                                            <input type="hidden" name="accion" value="cancelar">
                                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="fas fa-times me-2"></i>Cancelar
                                            </button>
                                        </form>
                                        <?php endif; ?>
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