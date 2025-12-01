<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['sesion_activa']) || !$_SESSION['sesion_activa']) {
    $_SESSION['error'] = 'Debes iniciar sesión para acceder a tu perfil';
    header('Location: ../login.php');
    exit();
}

require_once '../config/conexion.php';
require_once '../modelo/Usuario.php';
require_once '../modelo/Pedido.php';
require_once '../modelo/Reserva.php';

$modeloUsuario = new Usuario();
$modeloPedido = new Pedido();
$modeloReserva = new Reserva();

// Obtener datos del usuario
$usuario = $modeloUsuario->obtenerPorId($_SESSION['usuario_id']);

// Obtener historial de pedidos
$pedidos = $modeloPedido->obtenerPorUsuario($_SESSION['usuario_id']);

// Obtener historial de reservas
$reservas = $modeloReserva->obtenerPorUsuario($_SESSION['usuario_id']);

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'actualizar_perfil') {
        $datos = [
            'nombre' => trim($_POST['nombre']),
            'telefono' => trim($_POST['telefono'])
        ];
        
        $resultado = $modeloUsuario->actualizarPerfil($_SESSION['usuario_id'], $datos);
        
        if ($resultado['exito']) {
            $_SESSION['usuario_nombre'] = $datos['nombre'];
            $_SESSION['usuario_telefono'] = $datos['telefono'];
            $_SESSION['exito'] = $resultado['mensaje'];
            // Recargar datos
            $usuario = $modeloUsuario->obtenerPorId($_SESSION['usuario_id']);
        } else {
            $_SESSION['error'] = $resultado['mensaje'];
        }
        
        header('Location: perfil.php');
        exit();
    }
    
    if ($_POST['accion'] === 'cambiar_clave') {
        $claveActual = $_POST['clave_actual'];
        $claveNueva = $_POST['clave_nueva'];
        $confirmarClave = $_POST['confirmar_clave'];
        
        if ($claveNueva !== $confirmarClave) {
            $_SESSION['error'] = 'Las contraseñas nuevas no coinciden';
        } elseif (strlen($claveNueva) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
        } else {
            $resultado = $modeloUsuario->cambiarClave($_SESSION['usuario_id'], $claveActual, $claveNueva);
            
            if ($resultado['exito']) {
                $_SESSION['exito'] = $resultado['mensaje'];
            } else {
                $_SESSION['error'] = $resultado['mensaje'];
            }
        }
        
        header('Location: perfil.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Choco's Restaurante</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px;
        }
        .perfil-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0 50px;
            color: white;
        }
        .avatar-container {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .avatar-container i {
            font-size: 4rem;
            color: #667eea;
        }
        .perfil-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .perfil-card h5 {
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stat-box h3 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        .stat-box p {
            margin: 0;
            opacity: 0.9;
        }
        .pedido-item, .reserva-item {
            border-left: 4px solid #dc3545;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .pedido-item:hover, .reserva-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 600;
        }
        .nav-tabs .nav-link.active {
            color: #dc3545;
            border-bottom: 3px solid #dc3545;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Header del Perfil -->
    <section class="perfil-header text-center">
        <div class="container">
            <div class="avatar-container">
                <i class="fas fa-user-circle"></i>
            </div>
            <h1 class="display-5 fw-bold mb-2"><?php echo htmlspecialchars($usuario['nombre']); ?></h1>
            <p class="lead mb-0">
                <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($usuario['correo']); ?>
            </p>
            <span class="badge bg-light text-dark mt-3 px-4 py-2">
                <i class="fas fa-shield-alt me-2"></i><?php echo ucfirst($usuario['rol']); ?>
            </span>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-5">
        <div class="container">
            
            <!-- Mensajes -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['exito'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Columna Izquierda: Información del Usuario -->
                <div class="col-lg-4 mb-4">
                    
                    <!-- Estadísticas -->
                    <div class="stat-box">
                        <h3><?php echo count($pedidos); ?></h3>
                        <p><i class="fas fa-shopping-cart me-2"></i>Pedidos Realizados</p>
                    </div>
                    
                    <div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h3><?php echo count($reservas); ?></h3>
                        <p><i class="fas fa-calendar-alt me-2"></i>Reservas Realizadas</p>
                    </div>

                    <!-- Información del Usuario -->
                    <div class="perfil-card">
                        <h5><i class="fas fa-info-circle text-danger me-2"></i>Información Personal</h5>
                        
                        <div class="mb-3">
                            <label class="text-muted small">NOMBRE COMPLETO</label>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">CORREO ELECTRÓNICO</label>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($usuario['correo']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">TELÉFONO</label>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($usuario['telefono'] ?? 'No registrado'); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">ESTADO</label>
                            <p class="mb-0">
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i><?php echo ucfirst($usuario['estado']); ?>
                                </span>
                            </p>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small">MIEMBRO DESDE</label>
                            <p class="mb-0 fw-bold"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></p>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="perfil-card">
                        <h5><i class="fas fa-bolt text-danger me-2"></i>Acciones Rápidas</h5>
                        <div class="d-grid gap-2">
                            <a href="pedidos.php" class="btn btn-danger">
                                <i class="fas fa-shopping-cart me-2"></i>Hacer Nuevo Pedido
                            </a>
                            <a href="reservas.php" class="btn btn-outline-danger">
                                <i class="fas fa-calendar-alt me-2"></i>Nueva Reserva
                            </a>
                            <a href="menu.php" class="btn btn-outline-secondary">
                                <i class="fas fa-utensils me-2"></i>Ver Menú
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Tabs -->
                <div class="col-lg-8">
                    <div class="perfil-card">
                        
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs mb-4" id="perfilTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="editar-tab" data-bs-toggle="tab" 
                                        data-bs-target="#editar" type="button">
                                    <i class="fas fa-edit me-2"></i>Editar Perfil
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pedidos-tab" data-bs-toggle="tab" 
                                        data-bs-target="#pedidos" type="button">
                                    <i class="fas fa-shopping-bag me-2"></i>Mis Pedidos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reservas-tab" data-bs-toggle="tab" 
                                        data-bs-target="#reservasTab" type="button">
                                    <i class="fas fa-calendar-check me-2"></i>Mis Reservas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seguridad-tab" data-bs-toggle="tab" 
                                        data-bs-target="#seguridad" type="button">
                                    <i class="fas fa-lock me-2"></i>Seguridad
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="perfilTabsContent">
                            
                            <!-- Tab: Editar Perfil -->
                            <div class="tab-pane fade show active" id="editar" role="tabpanel">
                                <h5 class="mb-4"><i class="fas fa-user-edit text-danger me-2"></i>Editar Información</h5>
                                <form method="POST">
                                    <input type="hidden" name="accion" value="actualizar_perfil">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-user me-2"></i>Nombre Completo *
                                        </label>
                                        <input type="text" class="form-control form-control-lg" name="nombre" 
                                               value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-envelope me-2"></i>Correo Electrónico
                                        </label>
                                        <input type="email" class="form-control form-control-lg" 
                                               value="<?php echo htmlspecialchars($usuario['correo']); ?>" disabled>
                                        <small class="text-muted">El correo no se puede modificar</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-phone me-2"></i>Teléfono
                                        </label>
                                        <input type="tel" class="form-control form-control-lg" name="telefono" 
                                               value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>"
                                               placeholder="987654321">
                                    </div>

                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </form>
                            </div>

                            <!-- Tab: Mis Pedidos -->
                            <div class="tab-pane fade" id="pedidos" role="tabpanel">
                                <h5 class="mb-4"><i class="fas fa-shopping-bag text-danger me-2"></i>Historial de Pedidos</h5>
                                
                                <?php if (empty($pedidos)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                        <p class="text-muted">Aún no has realizado ningún pedido</p>
                                        <a href="pedidos.php" class="btn btn-danger">
                                            <i class="fas fa-plus me-2"></i>Hacer mi Primer Pedido
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($pedidos as $pedido): ?>
                                    <div class="pedido-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-shopping-bag text-danger me-2"></i>
                                                    Pedido #<?php echo $pedido['id_pedido']; ?>
                                                </h6>
                                                <p class="text-muted small mb-1">
                                                    <i class="fas fa-calendar me-2"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                                </p>
                                                <p class="text-muted small mb-0">
                                                    <i class="fas fa-map-marker-alt me-2"></i>
                                                    <?php echo htmlspecialchars($pedido['direccion_entrega']); ?>
                                                </p>
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
                                                <span class="badge badge-estado <?php echo $clase; ?> mb-2">
                                                    <?php echo ucfirst($pedido['estado']); ?>
                                                </span>
                                                <p class="mb-0 fw-bold text-danger">S/ <?php echo number_format($pedido['total'], 2); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Tab: Mis Reservas -->
                            <div class="tab-pane fade" id="reservasTab" role="tabpanel">
                                <h5 class="mb-4"><i class="fas fa-calendar-check text-danger me-2"></i>Historial de Reservas</h5>
                                
                                <?php if (empty($reservas)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                        <p class="text-muted">No tienes reservas registradas</p>
                                        <a href="reservas.php" class="btn btn-danger">
                                            <i class="fas fa-plus me-2"></i>Hacer mi Primera Reserva
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($reservas as $reserva): ?>
                                    <div class="reserva-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-2">
                                                    <i class="fas fa-calendar text-danger me-2"></i>
                                                    Reserva #<?php echo $reserva['id_reserva']; ?>
                                                </h6>
                                                <p class="mb-1">
                                                    <i class="fas fa-calendar-day me-2"></i>
                                                    <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($reserva['fecha'])); ?>
                                                </p>
                                                <p class="mb-1">
                                                    <i class="fas fa-clock me-2"></i>
                                                    <strong>Hora:</strong> <?php echo date('H:i', strtotime($reserva['hora'])); ?>
                                                </p>
                                                <p class="mb-0">
                                                    <i class="fas fa-users me-2"></i>
                                                    <strong>Personas:</strong> <?php echo $reserva['personas']; ?>
                                                </p>
                                            </div>
                                            <div>
                                                <?php
                                                $claseReserva = [
                                                    'pendiente' => 'bg-warning',
                                                    'confirmada' => 'bg-success',
                                                    'cancelada' => 'bg-danger'
                                                ];
                                                $clase = $claseReserva[$reserva['estado']] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge badge-estado <?php echo $clase; ?>">
                                                    <?php echo ucfirst($reserva['estado']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Tab: Seguridad -->
                            <div class="tab-pane fade" id="seguridad" role="tabpanel">
                                <h5 class="mb-4"><i class="fas fa-lock text-danger me-2"></i>Cambiar Contraseña</h5>
                                
                                <form method="POST">
                                    <input type="hidden" name="accion" value="cambiar_clave">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-key me-2"></i>Contraseña Actual *
                                        </label>
                                        <input type="password" class="form-control form-control-lg" 
                                               name="clave_actual" required placeholder="••••••••">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-lock me-2"></i>Nueva Contraseña *
                                        </label>
                                        <input type="password" class="form-control form-control-lg" 
                                               name="clave_nueva" required minlength="6" placeholder="••••••••">
                                        <small class="text-muted">Mínimo 6 caracteres</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-lock me-2"></i>Confirmar Nueva Contraseña *
                                        </label>
                                        <input type="password" class="form-control form-control-lg" 
                                               name="confirmar_clave" required minlength="6" placeholder="••••••••">
                                    </div>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Importante:</strong> Asegúrate de recordar tu nueva contraseña. 
                                        Se cerrará tu sesión después del cambio.
                                    </div>

                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-shield-alt me-2"></i>Cambiar Contraseña
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>