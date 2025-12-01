
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../modelo/Pedido.php';

$modeloPedido = new Pedido();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'actualizar_estado':
                $resultado = $modeloPedido->actualizarEstado($_POST['id_pedido'], $_POST['estado']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
                
            case 'cancelar':
                $resultado = $modeloPedido->cancelar($_POST['id_pedido']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
        }
    }
}

// Obtener filtros
$filtroEstado = $_GET['estado'] ?? '';
$filtros = [];
if (!empty($filtroEstado)) {
    $filtros['estado'] = $filtroEstado;
}

$pedidos = $modeloPedido->listarTodos($filtros);
$estadisticas = $modeloPedido->obtenerEstadisticas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Choco's Restaurante</title>
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
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .pedido-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .pedido-card:hover {
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
                <a href="gestionPedidos.php" class="active">
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
        <div class="mb-4">
            <h1 class="h3 mb-0">Gestión de Pedidos</h1>
            <p class="text-muted mb-0">Administra los pedidos de delivery</p>
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
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['pendientes'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-clock me-2"></i>Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['preparando'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-fire me-2"></i>En Preparación</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['enviados'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-motorcycle me-2"></i>Enviados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['entregados'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-check-circle me-2"></i>Entregados</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filtrar por Estado</label>
                        <select name="estado" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?php echo $filtroEstado === 'pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                            <option value="preparando" <?php echo $filtroEstado === 'preparando' ? 'selected' : ''; ?>>En Preparación</option>
                            <option value="enviado" <?php echo $filtroEstado === 'enviado' ? 'selected' : ''; ?>>Enviados</option>
                            <option value="entregado" <?php echo $filtroEstado === 'entregado' ? 'selected' : ''; ?>>Entregados</option>
                            <option value="cancelado" <?php echo $filtroEstado === 'cancelado' ? 'selected' : ''; ?>>Cancelados</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <a href="gestionPedidos.php" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Listado de Pedidos -->
        <div class="table-container">
            <?php if (empty($pedidos)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No hay pedidos registrados
                </div>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="mb-1">
                                            <i class="fas fa-shopping-bag text-danger me-2"></i>
                                            Pedido #<?php echo $pedido['id_pedido']; ?>
                                        </h5>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-user me-2"></i>
                                            <strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['usuario_nombre']); ?>
                                        </p>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-envelope me-2"></i>
                                            <?php echo htmlspecialchars($pedido['usuario_correo']); ?>
                                        </p>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-phone me-2"></i>
                                            <?php echo htmlspecialchars($pedido['telefono']); ?>
                                        </p>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion_entrega']); ?>
                                        </p>
                                        <?php if (!empty($pedido['notas'])): ?>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-comment me-2"></i>
                                            <strong>Notas:</strong> <?php echo htmlspecialchars($pedido['notas']); ?>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
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
                                        <p class="text-muted small mt-2 mb-0">
                                            <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-end">
                                    <h3 class="text-danger mb-3">S/ <?php echo number_format($pedido['total'], 2); ?></h3>
                                    
                                    <div class="btn-group-vertical w-100 gap-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="verDetalle(<?php echo $pedido['id_pedido']; ?>)">
                                            <i class="fas fa-eye me-2"></i>Ver Detalle
                                        </button>
                                        
                                        <?php if ($pedido['estado'] !== 'entregado' && $pedido['estado'] !== 'cancelado'): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-success dropdown-toggle w-100" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-edit me-2"></i>Cambiar Estado
                                            </button>
                                            <ul class="dropdown-menu w-100">
                                                <?php if ($pedido['estado'] !== 'preparando'): ?>
                                                <li>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="accion" value="actualizar_estado">
                                                        <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                                        <input type="hidden" name="estado" value="preparando">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-fire text-info me-2"></i>En Preparación
                                                        </button>
                                                    </form>
                                                </li>
                                                <?php endif; ?>
                                                
                                                <?php if ($pedido['estado'] !== 'enviado'): ?>
                                                <li>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="accion" value="actualizar_estado">
                                                        <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                                        <input type="hidden" name="estado" value="enviado">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-motorcycle text-primary me-2"></i>Enviado
                                                        </button>
                                                    </form>
                                                </li>
                                                <?php endif; ?>
                                                
                                                <li>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="accion" value="actualizar_estado">
                                                        <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                                        <input type="hidden" name="estado" value="entregado">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-check-circle text-success me-2"></i>Entregado
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('¿Cancelar este pedido?')">
                                            <input type="hidden" name="accion" value="cancelar">
                                            <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="fas fa-times me-2"></i>Cancelar Pedido
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Modal Ver Detalle -->
    <div class="modal fade" id="modalDetallePedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalle">
                    <div class="text-center py-4">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       function verDetalle(idPedido) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetallePedido'));
    const contenido = document.getElementById('contenidoDetalle');
    
    // Mostrar loading
    contenido.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="text-muted mt-2">Cargando detalles del pedido...</p>
        </div>
    `;
    
    modal.show();
    
    // Hacer petición AJAX
    fetch(`../../controlador/ajax/obtenerDetallePedido.php?id_pedido=${idPedido}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los detalles');
            }
            return response.json();
        })
        .then(pedido => {
            // Construir el HTML con los detalles
            let html = `
                <div class="mb-3">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-shopping-bag text-danger me-2"></i>
                        Pedido #${pedido.id_pedido}
                    </h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="fas fa-user me-2"></i>Cliente:</strong><br>
                                ${pedido.usuario_nombre}
                            </p>
                            <p class="mb-2">
                                <strong><i class="fas fa-envelope me-2"></i>Correo:</strong><br>
                                ${pedido.usuario_correo}
                            </p>
                            <p class="mb-2">
                                <strong><i class="fas fa-phone me-2"></i>Teléfono:</strong><br>
                                ${pedido.telefono}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="fas fa-map-marker-alt me-2"></i>Dirección:</strong><br>
                                ${pedido.direccion_entrega}
                            </p>
                            <p class="mb-2">
                                <strong><i class="fas fa-calendar me-2"></i>Fecha:</strong><br>
                                ${formatearFecha(pedido.fecha_pedido)}
                            </p>
                            <p class="mb-2">
                                <strong><i class="fas fa-info-circle me-2"></i>Estado:</strong><br>
                                <span class="badge ${getClaseEstado(pedido.estado)}">
                                    ${pedido.estado.toUpperCase()}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    ${pedido.notas ? `
                        <div class="alert alert-light mb-3">
                            <strong><i class="fas fa-comment me-2"></i>Notas:</strong><br>
                            ${pedido.notas}
                        </div>
                    ` : ''}
                </div>
                
                <div class="mb-3">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-utensils text-danger me-2"></i>
                        Platos Ordenados
                    </h6>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Plato</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            pedido.detalles.forEach(detalle => {
                html += `
                    <tr>
                        <td>${detalle.plato_nombre}</td>
                        <td class="text-center">${detalle.cantidad}</td>
                        <td class="text-end">S/ ${parseFloat(detalle.precio_unitario).toFixed(2)}</td>
                        <td class="text-end fw-bold">S/ ${parseFloat(detalle.subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });
            
            html += `
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                                    <td class="text-end fw-bold text-danger fs-5">
                                        S/ ${parseFloat(pedido.total).toFixed(2)}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;
            
            contenido.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            contenido.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar los detalles del pedido. Por favor, intenta nuevamente.
                </div>
            `;
        });
}

// Funciones auxiliares
function formatearFecha(fecha) {
    const date = new Date(fecha);
    const opciones = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleString('es-PE', opciones);
}

function getClaseEstado(estado) {
    const clases = {
        'pendiente': 'bg-warning',
        'preparando': 'bg-info',
        'enviado': 'bg-primary',
        'entregado': 'bg-success',
        'cancelado': 'bg-danger'
    };
    return clases[estado] || 'bg-secondary';
}
        
    </script>
</body>
</html>