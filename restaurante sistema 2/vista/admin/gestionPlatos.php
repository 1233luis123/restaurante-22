<?php
session_start();

// Verificar que sea administrador
if (!isset($_SESSION['sesion_activa']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

require_once __DIR__ . '/../../modelo/Plato.php';

$modeloPlato = new Plato();

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear':
                $resultado = $modeloPlato->crear($_POST);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
                
            case 'actualizar':
                $resultado = $modeloPlato->actualizar($_POST['id_plato'], $_POST);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
                
            case 'eliminar':
                $resultado = $modeloPlato->eliminar($_POST['id_plato']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
                
            case 'cambiar_disponibilidad':
                $resultado = $modeloPlato->cambiarDisponibilidad($_POST['id_plato'], $_POST['disponible']);
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = $resultado['exito'] ? 'success' : 'danger';
                break;
        }
    }
}

// Obtener filtros
$filtroCategoria = $_GET['categoria'] ?? '';
$filtros = [];
if (!empty($filtroCategoria)) {
    $filtros['categoria'] = $filtroCategoria;
}

$platos = $modeloPlato->listarTodos($filtros);
$estadisticas = $modeloPlato->obtenerEstadisticas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Platos - Choco's Restaurante</title>
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
        .card-plato {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
        }
        .card-plato:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .badge-disponible {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        .img-plato {
            height: 200px;
            object-fit: cover;
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
                <a href="gestionPlatos.php" class="active">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Gestión de Platos</h1>
                <p class="text-muted mb-0">Administra el menú del restaurante</p>
            </div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalNuevoPlato">
                <i class="fas fa-plus me-2"></i>Nuevo Plato
            </button>
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
                        <p class="mb-0"><i class="fas fa-utensils me-2"></i>Total Platos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['disponibles'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-check-circle me-2"></i>Disponibles</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['entradas'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-cookie-bite me-2"></i>Entradas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h3 class="mb-0"><?php echo $estadisticas['principales'] ?? 0; ?></h3>
                        <p class="mb-0"><i class="fas fa-drumstick-bite me-2"></i>Principales</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filtrar por Categoría</label>
                        <select name="categoria" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas las categorías</option>
                            <option value="entrada" <?php echo $filtroCategoria === 'entrada' ? 'selected' : ''; ?>>Entradas</option>
                            <option value="principal" <?php echo $filtroCategoria === 'principal' ? 'selected' : ''; ?>>Platos Principales</option>
                            <option value="postre" <?php echo $filtroCategoria === 'postre' ? 'selected' : ''; ?>>Postres</option>
                            <option value="bebida" <?php echo $filtroCategoria === 'bebida' ? 'selected' : ''; ?>>Bebidas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <a href="gestionPlatos.php" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Listado de Platos -->
        <div class="row g-4">
            <?php if (empty($platos)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay platos registrados
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($platos as $plato): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card card-plato">
                        <div class="position-relative">
                            <img src="../../assets/img/<?php echo htmlspecialchars($plato['imagen']); ?>" 
                                 class="card-img-top img-plato" 
                                 alt="<?php echo htmlspecialchars($plato['nombre']); ?>"
                                 onerror="this.src='../../assets/img/placeholder.jpg'">
                            <span class="badge badge-disponible <?php echo $plato['disponible'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $plato['disponible'] ? 'Disponible' : 'No disponible'; ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($plato['nombre']); ?></h5>
                            <p class="card-text text-muted small" style="min-height: 60px;">
                                <?php echo htmlspecialchars(substr($plato['descripcion'], 0, 80)); ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary"><?php echo ucfirst($plato['categoria']); ?></span>
                                <strong class="text-danger fs-5">S/ <?php echo number_format($plato['precio'], 2); ?></strong>
                            </div>
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick='editarPlato(<?php echo json_encode($plato, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Cambiar disponibilidad?')">
                                    <input type="hidden" name="accion" value="cambiar_disponibilidad">
                                    <input type="hidden" name="id_plato" value="<?php echo $plato['id_plato']; ?>">
                                    <input type="hidden" name="disponible" value="<?php echo $plato['disponible'] ? 0 : 1; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning" 
                                            title="<?php echo $plato['disponible'] ? 'Ocultar' : 'Mostrar'; ?>">
                                        <i class="fas fa-<?php echo $plato['disponible'] ? 'eye-slash' : 'eye'; ?>"></i>
                                    </button>
                                </form>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este plato? Esta acción no se puede deshacer.')">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_plato" value="<?php echo $plato['id_plato']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
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
    
    <!-- Modal Nuevo Plato -->
    <div class="modal fade" id="modalNuevoPlato" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle text-danger me-2"></i>Nuevo Plato
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="crear">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Plato *</label>
                            <input type="text" name="nombre" class="form-control" required 
                                   placeholder="Ej: Ceviche Clásico">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" 
                                      placeholder="Describe los ingredientes y características del plato"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio (S/) *</label>
                                <input type="number" name="precio" class="form-control" step="0.01" 
                                       min="0" required placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoría *</label>
                                <select name="categoria" class="form-select" required>
                                    <option value="entrada">Entrada</option>
                                    <option value="principal" selected>Plato Principal</option>
                                    <option value="postre">Postre</option>
                                    <option value="bebida">Bebida</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Imagen del Plato *</label>
                            <input type="file" name="imagen" class="form-control" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp" 
                                   id="inputImagen" required>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos: JPG, PNG, WEBP (Máx. 5MB)
                            </small>
                            
                            <!-- Preview de la imagen -->
                            <div id="preview" class="mt-3" style="display:none;">
                                <label class="form-label">Vista previa:</label>
                                <div class="text-center">
                                    <img id="imagenPreview" src="" alt="Preview" 
                                         style="max-width: 100%; height: 250px; object-fit: cover; border-radius: 10px; border: 2px solid #dee2e6;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="disponible" value="1" class="form-check-input" 
                                       id="disponibleCheck" checked>
                                <label class="form-check-label" for="disponibleCheck">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Marcar como disponible
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-2"></i>Guardar Plato
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Plato -->
    <div class="modal fade" id="modalEditarPlato" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEditar" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit text-primary me-2"></i>Editar Plato
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="id_plato" id="edit_id">
                        <input type="hidden" name="imagen_actual" id="edit_imagen_actual">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Plato *</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Precio (S/) *</label>
                                <input type="number" name="precio" id="edit_precio" class="form-control" 
                                       step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoría *</label>
                                <select name="categoria" id="edit_categoria" class="form-select" required>
                                    <option value="entrada">Entrada</option>
                                    <option value="principal">Plato Principal</option>
                                    <option value="postre">Postre</option>
                                    <option value="bebida">Bebida</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cambiar Imagen (opcional)</label>
                            <input type="file" name="imagen" class="form-control" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   id="inputImagenEdit">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Deja vacío si no deseas cambiar la imagen actual
                            </small>
                            
                            <!-- Imagen actual -->
                            <div class="mt-3">
                                <label class="form-label">Imagen actual:</label>
                                <div class="text-center">
                                    <img id="edit_imagen_preview" src="" alt="Imagen actual" 
                                         style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid #dee2e6;">
                                </div>
                            </div>
                            
                            <!-- Preview nueva imagen -->
                            <div id="previewEdit" class="mt-3" style="display:none;">
                                <label class="form-label text-success">
                                    <i class="fas fa-check-circle me-1"></i>Nueva imagen seleccionada:
                                </label>
                                <div class="text-center">
                                    <img id="imagenPreviewEdit" src="" alt="Preview" 
                                         style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid #28a745;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="disponible" value="1" class="form-check-input" 
                                       id="edit_disponible">
                                <label class="form-check-label" for="edit_disponible">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Marcar como disponible
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Plato
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview de imagen al crear nuevo plato
        document.getElementById('inputImagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño (5MB máximo)
                if (file.size > 5 * 1024 * 1024) {
                    alert('La imagen es muy grande. Máximo 5MB.');
                    this.value = '';
                    document.getElementById('preview').style.display = 'none';
                    return;
                }
                
                // Validar tipo
                const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert('Tipo de archivo no permitido. Solo JPG, PNG, WEBP');
                    this.value = '';
                    document.getElementById('preview').style.display = 'none';
                    return;
                }
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagenPreview').src = e.target.result;
                    document.getElementById('preview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('preview').style.display = 'none';
            }
        });

        // Preview de imagen al editar plato
        document.getElementById('inputImagenEdit').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño
                if (file.size > 5 * 1024 * 1024) {
                    alert('La imagen es muy grande. Máximo 5MB.');
                    this.value = '';
                    document.getElementById('previewEdit').style.display = 'none';
                    return;
                }
                
                // Validar tipo
                const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert('Tipo de archivo no permitido. Solo JPG, PNG, WEBP');
                    this.value = '';
                    document.getElementById('previewEdit').style.display = 'none';
                    return;
                }
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagenPreviewEdit').src = e.target.result;
                    document.getElementById('previewEdit').style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('previewEdit').style.display = 'none';
            }
        });

        // Función para editar plato
        function editarPlato(plato) {
            document.getElementById('edit_id').value = plato.id_plato;
            document.getElementById('edit_nombre').value = plato.nombre;
            document.getElementById('edit_descripcion').value = plato.descripcion || '';
            document.getElementById('edit_precio').value = plato.precio;
            document.getElementById('edit_categoria').value = plato.categoria;
            document.getElementById('edit_imagen_actual').value = plato.imagen;
            document.getElementById('edit_imagen_preview').src = '../../assets/img/' + plato.imagen;
            document.getElementById('edit_disponible').checked = plato.disponible == 1;
            document.getElementById('previewEdit').style.display = 'none';
            document.getElementById('inputImagenEdit').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('modalEditarPlato'));
            modal.show();
        }
        
        // Limpiar formulario al cerrar modal de crear
        document.getElementById('modalNuevoPlato').addEventListener('hidden.bs.modal', function () {
            document.querySelector('#modalNuevoPlato form').reset();
            document.getElementById('preview').style.display = 'none';
        });
    </script>
</body>
</html>