<?php
session_start();
require_once '../config/conexion.php';
require_once '../modelo/Plato.php';
require_once '../modelo/Pedido.php';

// Verificar sesión
if (!isset($_SESSION['sesion_activa']) || !$_SESSION['sesion_activa']) {
    $_SESSION['error'] = 'Debes iniciar sesión para hacer pedidos';
    header('Location: ../login.php');
    exit();
}

$modeloPlato = new Plato();
$modeloPedido = new Pedido();

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar plato al carrito
if (isset($_GET['agregar'])) {
    $platoId = intval($_GET['agregar']);
    $plato = $modeloPlato->obtenerPorId($platoId);
    
    if ($plato && $plato['disponible']) {
        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id_plato'] == $platoId) {
                $item['cantidad']++;
                $encontrado = true;
                break;
            }
        }
        
        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'id_plato' => $plato['id_plato'],
                'nombre' => $plato['nombre'],
                'precio' => $plato['precio'],
                'imagen' => $plato['imagen'],
                'cantidad' => 1
            ];
        }
        
        $_SESSION['exito'] = 'Plato agregado al carrito';
        header('Location: pedidos.php');
        exit();
    }
}

// Actualizar cantidad
if (isset($_POST['actualizar_cantidad'])) {
    $platoId = intval($_POST['plato_id']);
    $cantidad = intval($_POST['cantidad']);
    
    foreach ($_SESSION['carrito'] as $key => &$item) {
        if ($item['id_plato'] == $platoId) {
            if ($cantidad > 0 && $cantidad <= 20) {
                $item['cantidad'] = $cantidad;
            } elseif ($cantidad <= 0) {
                unset($_SESSION['carrito'][$key]);
            }
            break;
        }
    }
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    header('Location: pedidos.php');
    exit();
}

// Eliminar del carrito
if (isset($_GET['eliminar'])) {
    $platoId = intval($_GET['eliminar']);
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id_plato'] == $platoId) {
            unset($_SESSION['carrito'][$key]);
            break;
        }
    }
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    $_SESSION['exito'] = 'Plato eliminado del carrito';
    header('Location: pedidos.php');
    exit();
}

// Vaciar carrito
if (isset($_GET['vaciar'])) {
    $_SESSION['carrito'] = [];
    $_SESSION['exito'] = 'Carrito vaciado';
    header('Location: pedidos.php');
    exit();
}

// Calcular total
$total = 0;
$cantidadTotal = 0;
foreach ($_SESSION['carrito'] as $item) {
    $subtotal = $item['precio'] * $item['cantidad'];
    $total += $subtotal;
    $cantidadTotal += $item['cantidad'];
}

// Obtener platos disponibles agrupados por categoría
$platos = $modeloPlato->listarTodos(['disponible' => 1]);
$platosPorCategoria = [];
foreach ($platos as $plato) {
    $platosPorCategoria[$plato['categoria']][] = $plato;
}

// Ordenar categorías
$ordenCategorias = ['entrada', 'principal', 'postre', 'bebida'];
uksort($platosPorCategoria, function($a, $b) use ($ordenCategorias) {
    return array_search($a, $ordenCategorias) - array_search($b, $ordenCategorias);
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacer Pedido - Choco's Restaurante</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px;
        }
        .carrito-sidebar {
            position: sticky;
            top: 90px;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            max-height: calc(100vh - 110px);
            overflow-y: auto;
        }
        .carrito-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            transition: all 0.3s ease;
        }
        .carrito-item:hover {
            background: #f8f9fa;
            padding-left: 10px;
            border-radius: 8px;
        }
        .carrito-item:last-child {
            border-bottom: none;
        }
        .carrito-vacio {
            text-align: center;
            padding: 40px 20px;
        }
        .carrito-vacio i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        .plato-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: 100%;
        }
        .plato-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .plato-img {
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }
        .categoria-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        .btn-agregar {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-agregar:hover {
            background: linear-gradient(45deg, #c82333, #dc3545);
            transform: scale(1.05);
        }
        .cantidad-input {
            width: 60px;
            text-align: center;
            font-weight: bold;
        }
        .total-badge {
            font-size: 1.8rem;
            padding: 15px 25px;
            border-radius: 12px;
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        .badge-stock {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        @media (max-width: 991px) {
            .carrito-sidebar {
                position: relative;
                top: 0;
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Hero -->
    <section class="py-5 bg-danger text-white">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-shopping-cart me-3"></i>Hacer Pedido
            </h1>
            <p class="lead">Delivery rápido a tu domicilio en 30-45 minutos</p>
            <?php if ($cantidadTotal > 0): ?>
                <div class="badge total-badge mt-3">
                    <i class="fas fa-shopping-basket me-2"></i>
                    <?php echo $cantidadTotal; ?> producto<?php echo $cantidadTotal > 1 ? 's' : ''; ?> 
                    | S/ <?php echo number_format($total, 2); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Mensajes -->
    <div class="container mt-3">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenido Principal -->
    <section class="py-4">
        <div class="container">
            <div class="row">
                <!-- Platos Disponibles -->
                <div class="col-lg-8 mb-4">
                    <h3 class="mb-4">
                        <i class="fas fa-utensils text-danger me-2"></i>
                        Selecciona tus Platos
                    </h3>

                    <?php foreach ($platosPorCategoria as $categoria => $platosCategoria): ?>
                    <div class="mb-5" id="categoria-<?php echo $categoria; ?>">
                        <div class="categoria-header">
                            <h4 class="mb-0">
                                <i class="fas fa-<?php 
                                    echo $categoria === 'entrada' ? 'seedling' : 
                                        ($categoria === 'principal' ? 'drumstick-bite' : 
                                        ($categoria === 'postre' ? 'ice-cream' : 'glass-cheers')); 
                                ?> me-2"></i>
                                <?php echo ucfirst($categoria); ?>s
                                <span class="badge bg-white text-danger"><?php echo count($platosCategoria); ?></span>
                            </h4>
                        </div>
                        
                        <div class="row g-3">
                            <?php foreach ($platosCategoria as $plato): ?>
                            <div class="col-md-6">
                                <div class="card plato-card">
                                    <div class="position-relative">
                                        <img src="../assets/img/<?php echo htmlspecialchars($plato['imagen']); ?>" 
                                             class="card-img-top plato-img" 
                                             alt="<?php echo htmlspecialchars($plato['nombre']); ?>">
                                        <span class="badge bg-success badge-stock">Disponible</span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title mb-2"><?php echo htmlspecialchars($plato['nombre']); ?></h6>
                                        <p class="card-text small text-muted mb-2">
                                            <?php echo htmlspecialchars(substr($plato['descripcion'], 0, 60)); ?>...
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="text-danger mb-0">S/ <?php echo number_format($plato['precio'], 2); ?></h5>
                                            <a href="?agregar=<?php echo $plato['id_plato']; ?>" 
                                               class="btn btn-danger btn-sm btn-agregar">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Carrito -->
                <div class="col-lg-4">
                    <div class="carrito-sidebar">
                        <h4 class="mb-4">
                            <i class="fas fa-shopping-cart me-2 text-danger"></i>
                            Mi Carrito
                            <?php if (!empty($_SESSION['carrito'])): ?>
                                <span class="badge bg-danger"><?php echo count($_SESSION['carrito']); ?></span>
                            <?php endif; ?>
                        </h4>

                        <?php if (empty($_SESSION['carrito'])): ?>
                            <div class="carrito-vacio">
                                <i class="fas fa-shopping-cart"></i>
                                <p class="text-muted">Tu carrito está vacío</p>
                                <p class="small text-muted">Agrega platos para comenzar tu pedido</p>
                            </div>
                        <?php else: ?>
                            <!-- Items del carrito -->
                            <div class="carrito-items mb-3">
                                <?php foreach ($_SESSION['carrito'] as $item): ?>
                                <div class="carrito-item">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="../assets/img/<?php echo htmlspecialchars($item['imagen']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['nombre']); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                                             class="me-3">
                                        <div class="flex-grow-1">
                                            <strong class="small d-block"><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                            <small class="text-muted">S/ <?php echo number_format($item['precio'], 2); ?></small>
                                        </div>
                                        <a href="?eliminar=<?php echo $item['id_plato']; ?>" 
                                           class="btn btn-sm btn-link text-danger p-0"
                                           onclick="return confirm('¿Eliminar este producto?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <form method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="actualizar_cantidad" value="1">
                                            <input type="hidden" name="plato_id" value="<?php echo $item['id_plato']; ?>">
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="this.nextElementSibling.value = Math.max(0, parseInt(this.nextElementSibling.value) - 1); this.form.submit();">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" 
                                                   min="0" max="20" class="form-control form-control-sm cantidad-input" 
                                                   onchange="this.form.submit()" readonly>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="this.previousElementSibling.value = Math.min(20, parseInt(this.previousElementSibling.value) + 1); this.form.submit();">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                        <strong class="text-danger">S/ <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></strong>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Resumen -->
                            <div class="border-top pt-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong>S/ <?php echo number_format($total, 2); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Delivery:</span>
                                    <strong class="text-success">GRATIS</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>TOTAL:</strong>
                                    <h4 class="text-danger mb-0">S/ <?php echo number_format($total, 2); ?></h4>
                                </div>
                            </div>

                            <!-- Botones -->
                            <button class="btn btn-success w-100 mb-2 btn-lg" data-bs-toggle="modal" data-bs-target="#modalPedido">
                                <i class="fas fa-check-circle me-2"></i>Realizar Pedido
                            </button>
                            <a href="?vaciar=1" class="btn btn-outline-danger w-100" 
                               onclick="return confirm('¿Vaciar el carrito?')">
                                <i class="fas fa-trash me-2"></i>Vaciar Carrito
                            </a>
                            
                            <div class="alert alert-info mt-3 small">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Tiempo de entrega:</strong> 30-45 minutos
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Realizar Pedido -->
    <div class="modal fade" id="modalPedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="../controlador/controladorPedido.php" method="POST">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Datos de Entrega
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marked-alt me-2 text-danger"></i>
                                Dirección de Entrega *
                            </label>
                            <textarea class="form-control" name="direccion_entrega" rows="3" required
                                      placeholder="Av. Principal 123, Dpto 301, Referencia: Frente al parque"></textarea>
                            <small class="text-muted">Incluye referencias para encontrar más fácil tu domicilio</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-phone me-2 text-danger"></i>
                                Teléfono de Contacto *
                            </label>
                            <input type="tel" class="form-control" name="telefono" required
                                   value="<?php echo htmlspecialchars($_SESSION['usuario_telefono'] ?? ''); ?>"
                                   placeholder="987654321" pattern="[0-9]{9}">
                            <small class="text-muted">9 dígitos sin espacios</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-comment me-2 text-danger"></i>
                                Notas Adicionales (Opcional)
                            </label>
                            <textarea class="form-control" name="notas" rows="2"
                                      placeholder="Indicaciones especiales, alergias, cambiar algún ingrediente, etc."></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Tiempo estimado de entrega:</strong> 30-45 minutos aproximadamente
                        </div>

                        <!-- Resumen del Pedido -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-receipt me-2 text-danger"></i>
                                    Resumen del Pedido
                                </h6>
                                <?php foreach ($_SESSION['carrito'] as $item): ?>
                                <div class="d-flex justify-content-between small mb-2">
                                    <span>
                                        <strong><?php echo $item['cantidad']; ?>x</strong> 
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </span>
                                    <span class="text-danger fw-bold">
                                        S/ <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>TOTAL A PAGAR:</strong>
                                    <h5 class="text-danger mb-0">S/ <?php echo number_format($total, 2); ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle me-2"></i>Confirmar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Prevenir envío duplicado del formulario
        document.querySelector('#modalPedido form').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        });
        
        // Auto-scroll suave a categorías
        document.querySelectorAll('a[href^="#categoria-"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>