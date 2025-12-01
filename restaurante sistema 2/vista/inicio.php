
<?php
session_start();
require_once '../config/conexion.php';
require_once '../modelo/Plato.php';

$sesion_activa = isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true;
$modeloPlato = new Plato();
$platosDestacados = $modeloPlato->listarTodos(['disponible' => 1]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Choco's Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>
<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 70px;">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Bienvenido a Choco's</h1>
                    <p class="lead mb-4">
                        Disfruta de la mejor gastronomía peruana en un ambiente acogedor. 
                        Tradición, sabor y calidad en cada plato.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="menu.php" class="btn btn-danger btn-lg">
                            <i class="fas fa-utensils me-2"></i>Ver Menú
                        </a>
                        <a href="reservas.php" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-calendar-alt me-2"></i>Reservar Mesa
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="../assets/img/ceviche.jpg" alt="Plato destacado" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Características -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-3">
                    <div class="feature-box">
                        <i class="fas fa-concierge-bell fa-3x text-danger mb-3"></i>
                        <h4>Servicio de Calidad</h4>
                        <p class="text-muted">Atención personalizada para cada cliente</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                        <h4>Ingredientes Frescos</h4>
                        <p class="text-muted">Seleccionados cuidadosamente cada día</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <i class="fas fa-motorcycle fa-3x text-primary mb-3"></i>
                        <h4>Delivery Rápido</h4>
                        <p class="text-muted">Llevamos tu pedido en tiempo récord</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-box">
                        <i class="fas fa-star fa-3x text-warning mb-3"></i>
                        <h4>Sabor Auténtico</h4>
                        <p class="text-muted">Recetas tradicionales peruanas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Platos Destacados -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Platos Destacados</h2>
                <p class="lead text-muted">Lo mejor de nuestra cocina</p>
            </div>
            <div class="row g-4">
                <?php 
                $destacados = array_slice($platosDestacados, 0, 4);
                foreach ($destacados as $plato): 
                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <img src="../assets/img/<?php echo htmlspecialchars($plato['imagen']); ?>" 
                             class="card-img-top" 
                             style="height: 200px; object-fit: cover;"
                             alt="<?php echo htmlspecialchars($plato['nombre']); ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($plato['nombre']); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                            <p class="h4 text-danger">S/ <?php echo number_format($plato['precio'], 2); ?></p>
                            <a href="pedidos.php" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-shopping-cart me-2"></i>Ordenar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="menu.php" class="btn btn-danger btn-lg">
                    Ver Menú Completo <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/scripts.js"></script>
</body>
</html>