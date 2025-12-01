
<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../modelo/Usuario.php';

$modeloUsuario = new Usuario();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        // Aquí puedes agregar acciones como cambiar rol, activar/desactivar usuarios
        $mensaje = "Acción procesada";
        $tipoMensaje = "success";
    }
}

$usuarios = $modeloUsuario->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Choco's Restaurante</title>
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
        .usuario-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .usuario-card:hover {
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
                <a href="gestionUsuarios.php" class="active">
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
            <h1 class="h3 mb-0">Gestión de Usuarios</h1>
            <p class="text-muted mb-0">Administra los usuarios del sistema</p>
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
                        <h3 class="mb-0"><?php echo count($usuarios); ?></h3>
                        <p class="mb-0"><i class="fas fa-users me-2"></i>Total Usuarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h3 class="mb-0">
                            <?php echo count(array_filter($usuarios, fn($u) => $u['rol'] === 'admin')); ?>
                        </h3>
                        <p class="mb-0"><i class="fas fa-user-shield me-2"></i>Administradores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="mb-0">
                            <?php echo count(array_filter($usuarios, fn($u) => $u['rol'] === 'cliente')); ?>
                        </h3>
                        <p class="mb-0"><i class="fas fa-user me-2"></i>Clientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h3 class="mb-0">
                            <?php echo count(array_filter($usuarios, fn($u) => $u['estado'] === 'activo')); ?>
                        </h3>
                        <p class="mb-0"><i class="fas fa-check-circle me-2"></i>Activos</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Listado de Usuarios -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Usuarios</h5>
                <div>
                    <input type="text" id="buscarUsuario" class="form-control form-control-sm" 
                           placeholder="Buscar usuario...">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><strong>#<?php echo $usuario['id_usuario']; ?></strong></td>
                                <td>
                                    <i class="fas fa-user-circle text-<?php echo $usuario['rol'] === 'admin' ? 'danger' : 'primary'; ?> me-2"></i>
                                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['telefono'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $usuario['rol'] === 'admin' ? 'danger' : 'primary'; ?>">
                                        <?php echo ucfirst($usuario['rol']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $usuario['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($usuario['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="verDetalle(<?php echo htmlspecialchars(json_encode($usuario)); ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Modal Ver Detalle -->
    <div class="modal fade" id="modalDetalleUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="contenidoDetalle">
                    <!-- Se llenará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verDetalle(usuario) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleUsuario'));
            
            const contenido = `
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="text-center">
                            <i class="fas fa-user-circle fa-5x text-${usuario.rol === 'admin' ? 'danger' : 'primary'} mb-3"></i>
                            <h4>${usuario.nombre}</h4>
                            <span class="badge bg-${usuario.rol === 'admin' ? 'danger' : 'primary'} mb-3">
                                ${usuario.rol.toUpperCase()}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <p><strong><i class="fas fa-envelope me-2"></i>Correo:</strong></p>
                    </div>
                    <div class="col-6">
                        <p>${usuario.correo}</p>
                    </div>
                    <div class="col-6">
                        <p><strong><i class="fas fa-phone me-2"></i>Teléfono:</strong></p>
                    </div>
                    <div class="col-6">
                        <p>${usuario.telefono || 'No registrado'}</p>
                    </div>
                    <div class="col-6">
                        <p><strong><i class="fas fa-circle me-2"></i>Estado:</strong></p>
                    </div>
                    <div class="col-6">
                        <p><span class="badge bg-${usuario.estado === 'activo' ? 'success' : 'secondary'}">
                            ${usuario.estado.toUpperCase()}
                        </span></p>
                    </div>
                    <div class="col-6">
                        <p><strong><i class="fas fa-calendar me-2"></i>Registro:</strong></p>
                    </div>
                    <div class="col-6">
                        <p>${new Date(usuario.fecha_registro).toLocaleDateString('es-PE')}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('contenidoDetalle').innerHTML = contenido;
            modal.show();
        }
        
        // Búsqueda en tiempo real
        document.getElementById('buscarUsuario').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const filas = document.querySelectorAll('tbody tr');
            
            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(filtro) ? '' : 'none';
            });
        });
    </script>
</body>
</html>