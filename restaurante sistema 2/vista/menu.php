<?php
session_start();
require_once '../config/conexion.php';
require_once '../modelo/Plato.php';

$modeloPlato = new Plato();

// Obtener categoría filtrada
$categoriaFiltro = $_GET['categoria'] ?? '';
$filtros = [];
if (!empty($categoriaFiltro)) {
    $filtros['categoria'] = $categoriaFiltro;
}
$filtros['disponible'] = 1;

$platos = $modeloPlato->listarTodos($filtros);

// Agrupar platos por categoría
$platosPorCategoria = [];
foreach ($platos as $plato) {
    $platosPorCategoria[$plato['categoria']][] = $plato;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestro Menú - Choco's Restaurante</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    
    <style>
        .menu-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            padding: 150px 0 80px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .menu-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/img/pattern.png');
            opacity: 0.1;
        }
        
        .filter-bar {
            position: sticky;
            top: 70px;
            background: white;
            z-index: 100;
            padding: 20px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filter-btn {
            padding: 12px 30px;
            border-radius: 50px;
            border: 2px solid #dee2e6;
            background: white;
            color: #495057;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        
        .filter-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border-color: #dc3545;
            color: white;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .categoria-section {
            margin-bottom: 60px;
        }
        
        .categoria-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 5px solid #dc3545;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .plato-card {
            height: 100%;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            background: white;
        }
        
        .plato-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .plato-img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .plato-card:hover .plato-img {
            transform: scale(1.15);
        }
        
        .precio-tag {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(220, 53, 69, 0.95);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 800;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .badge-categoria {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Header del Menú -->
    <section class="menu-header">
        <div class="container text-center position-relative">
            <h1 class="display-3 fw-bold mb-4">
                <i class="fas fa-book-open me-3"></i>Nuestro Menú
            </h1>
            <p class="lead fs-4">Explora nuestra selección de auténticos platos peruanos</p>
            <div class="mt-4">
                <span class="badge bg-light text-dark fs-6 px-4 py-2 me-2">
                    <i class="fas fa-check-circle text-success me-2"></i><?php echo count($platos); ?> Platos Disponibles
                </span>
                <span class="badge bg-light text-dark fs-6 px-4 py-2">
                    <i class="fas fa-leaf text-success me-2"></i>Ingredientes Frescos
                </span>
            </div>
        </div>
    </section>

    <!-- Barra de Filtros -->
    <div class="filter-bar">
        <div class="container">
            <div class="text-center">
                <a href="menu.php" class="filter-btn <?php echo empty($categoriaFiltro) ? 'active' : ''; ?>">
                    <i class="fas fa-list me-2"></i>Todos
                </a>
                <a href="?categoria=entrada" class="filter-btn <?php echo $categoriaFiltro === 'entrada' ? 'active' : ''; ?>">
                    <i class="fas fa-seedling me-2"></i>Entradas
                </a>
                <a href="?categoria=principal" class="filter-btn <?php echo $categoriaFiltro === 'principal' ? 'active' : ''; ?>">
                    <i class="fas fa-drumstick-bite me-2"></i>Platos Principales
                </a>
                <a href="?categoria=postre" class="filter-btn <?php echo $categoriaFiltro === 'postre' ? 'active' : ''; ?>">
                    <i class="fas fa-ice-cream me-2"></i>Postres
                </a>
                <a href="?categoria=bebida" class="filter-btn <?php echo $categoriaFiltro === 'bebida' ? 'active' : ''; ?>">
                    <i class="fas fa-glass-cheers me-2"></i>Bebidas
                </a>
            </div>
        </div>
    </div>

    <!-- Contenido del Menú -->
    <section class="py-5">
        <div class="container">
            
            <?php if (empty($platos)): ?>
                <div class="empty-state">
                    <i class="fas fa-utensils"></i>
                    <h3 class="fw-bold mb-3">No hay platos disponibles</h3>
                    <p class="text-muted mb-4">No se encontraron platos en esta categoría</p>
                    <a href="menu.php" class="btn btn-danger">
                        <i class="fas fa-arrow-left me-2"></i>Ver Todos los Platos
                    </a>
                </div>
            <?php else: ?>
                
                <?php if (empty($categoriaFiltro)): ?>
                    <!-- Vista por categorías -->
                    <?php 
                    $iconos = [
                        'entrada' => 'fa-seedling',
                        'principal' => 'fa-drumstick-bite',
                        'postre' => 'fa-ice-cream',
                        'bebida' => 'fa-glass-cheers'
                    ];
                    $colores = [
                        'entrada' => 'primary',
                        'principal' => 'success',
                        'postre' => 'warning',
                        'bebida' => 'info'
                    ];
                    
                    foreach ($platosPorCategoria as $categoria => $platosCategoria): 
                    ?>
                    <div class="categoria-section" id="categoria-<?php echo $categoria; ?>">
                        <div class="categoria-header">
                            <h2 class="fw-bold mb-0">
                                <i class="fas <?php echo $iconos[$categoria] ?? 'fa-utensils'; ?> text-danger me-3"></i>
                                <?php echo ucfirst($categoria); ?>s
                                <span class="badge bg-danger ms-3"><?php echo count($platosCategoria); ?></span>
                            </h2>
                        </div>
                        
                        <div class="row g-4">
                            <?php foreach ($platosCategoria as $plato): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card plato-card">
                                    <div class="position-relative overflow-hidden">
                                        <img src="../assets/img/<?php echo htmlspecialchars($plato['imagen']); ?>" 
                                             class="card-img-top plato-img" 
                                             alt="<?php echo htmlspecialchars($plato['nombre']); ?>">
                                        <span class="badge badge-categoria bg-<?php echo $colores[$plato['categoria']]; ?>">
                                            <?php echo ucfirst($plato['categoria']); ?>
                                        </span>
                                        <span class="precio-tag">
                                            S/ <?php echo number_format($plato['precio'], 2); ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($plato['nombre']); ?></h5>
                                        <p class="card-text text-muted small mb-3">
                                            <?php echo htmlspecialchars($plato['descripcion']); ?>
                                        </p>
                                        <?php if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa']): ?>
                                            <a href="pedidos.php?agregar=<?php echo $plato['id_plato']; ?>" 
                                               class="btn btn-danger w-100">
                                                <i class="fas fa-shopping-cart me-2"></i>Agregar
                                            </a>
                                        <?php else: ?>
                                            <a href="../login.php" class="btn btn-outline-danger w-100">
                                                <i class="fas fa-sign-in-alt me-2"></i>Inicia Sesión
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                <?php else: ?>
                    <!-- Vista filtrada -->
                    <div class="row g-4">
                        <?php foreach ($platos as $plato): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card plato-card">
                                <div class="position-relative overflow-hidden">
                                    <img src="../assets/img/<?php echo htmlspecialchars($plato['imagen']); ?>" 
                                         class="card-img-top plato-img" 
                                         alt="<?php echo htmlspecialchars($plato['nombre']); ?>">
                                    <span class="badge badge-categoria bg-<?php echo $colores[$plato['categoria']] ?? 'primary'; ?>">
                                        <?php echo ucfirst($plato['categoria']); ?>
                                    </span>
                                    <span class="precio-tag">
                                        S/ <?php echo number_format($plato['precio'], 2); ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-2"><?php echo htmlspecialchars($plato['nombre']); ?></h5>
                                    <p class="card-text text-muted small mb-3">
                                        <?php echo htmlspecialchars($plato['descripcion']); ?>
                                    </p>
                                    <?php if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa']): ?>
                                        <a href="pedidos.php?agregar=<?php echo $plato['id_plato']; ?>" 
                                           class="btn btn-danger w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Agregar
                                        </a>
                                    <?php else: ?>
                                        <a href="../login.php" class="btn btn-outline-danger w-100">
                                            <i class="fas fa-sign-in-alt me-2"></i>Inicia Sesión
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
            
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-danger text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">¿Listo para Ordenar?</h2>
            <p class="lead mb-4">Haz tu pedido ahora y disfruta en casa</p>
            <a href="pedidos.php" class="btn btn-light btn-lg px-5">
                <i class="fas fa-motorcycle me-2"></i>Hacer Pedido Ahora
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animación de entrada
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.plato-card').forEach(el => observer.observe(el));
        
        // Smooth scroll a categorías
        document.querySelectorAll('a[href^="#categoria"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>