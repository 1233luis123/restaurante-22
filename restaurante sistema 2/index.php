<?php 
session_start();
require_once 'config/conexion.php';
require_once 'modelo/Plato.php';

$modeloPlato = new Plato();
$platosDestacados = $modeloPlato->listarTodos(['disponible' => 1]);
$platosDestacados = array_slice($platosDestacados, 0, 4);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Choco's Restaurante - Auténtica cocina peruana en Trujillo">
    <meta name="keywords" content="restaurante, comida peruana, ceviche, lomo saltado, delivery">
    <title>Choco's Restaurante - Sabores Auténticos del Perú</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilo.css">
    
    <style>
        .hero-home {
            position: relative;
            height: 100vh;
            overflow: hidden;
        }
        
        .hero-slide {
            position: relative;
            height: 100%;
        }
        
        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.6);
        }
        
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
            width: 90%;
            max-width: 800px;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: fadeInUp 1s ease;
        }
        
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            animation: fadeInUp 1.2s ease;
        }
        
        .hero-buttons {
            animation: fadeInUp 1.4s ease;
        }
        
        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
        
        .stat-box {
            text-align: center;
            padding: 30px;
        }
        
        .stat-box h3 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .cta-section {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/img/pattern.png');
            opacity: 0.1;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-home">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            </div>
            
            <div class="carousel-inner h-100">
                <div class="carousel-item active hero-slide">
                    <img src="assets/img/ceviche.jpg" alt="Ceviche">
                    <div class="hero-content">
                        <h1 class="display-3 fw-bold">Sabores Auténticos del Perú</h1>
                        <p class="lead">Descubre la mejor gastronomía peruana en cada plato</p>
                        <div class="hero-buttons">
                            <a href="vista/menu.php" class="btn btn-danger btn-lg me-3 px-5">
                                <i class="fas fa-book-open me-2"></i>Ver Menú
                            </a>
                            <a href="vista/reservas.php" class="btn btn-outline-light btn-lg px-5">
                                <i class="fas fa-calendar-check me-2"></i>Reservar Mesa
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item hero-slide">
                    <img src="assets/img/saltado.jpg" alt="Lomo Saltado">
                    <div class="hero-content">
                        <h1 class="display-3 fw-bold">Tradición en Cada Bocado</h1>
                        <p class="lead">Recetas tradicionales preparadas con pasión</p>
                        <div class="hero-buttons">
                            <a href="vista/pedidos.php" class="btn btn-warning btn-lg me-3 px-5">
                                <i class="fas fa-motorcycle me-2"></i>Pedir Delivery
                            </a>
                            <a href="vista/contacto.php" class="btn btn-outline-light btn-lg px-5">
                                <i class="fas fa-phone me-2"></i>Contactar
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item hero-slide">
                    <img src="assets/img/arrozchaufa.jpg" alt="Arroz Chaufa">
                    <div class="hero-content">
                        <h1 class="display-3 fw-bold">Experiencia Gastronómica Única</h1>
                        <p class="lead">Más de 15 años deleitando paladares</p>
                        <div class="hero-buttons">
                            <a href="#menu" class="btn btn-success btn-lg me-3 px-5">
                                <i class="fas fa-utensils me-2"></i>Explorar
                            </a>
                            <a href="vista/reservas.php" class="btn btn-outline-light btn-lg px-5">
                                <i class="fas fa-heart me-2"></i>Reservar Ahora
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <!-- Estadísticas -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 stat-box">
                    <h3><i class="fas fa-users"></i> 5000+</h3>
                    <p class="mb-0">Clientes Satisfechos</p>
                </div>
                <div class="col-md-3 stat-box">
                    <h3><i class="fas fa-utensils"></i> 50+</h3>
                    <p class="mb-0">Platos en Menú</p>
                </div>
                <div class="col-md-3 stat-box">
                    <h3><i class="fas fa-trophy"></i> 15+</h3>
                    <p class="mb-0">Años de Experiencia</p>
                </div>
                <div class="col-md-3 stat-box">
                    <h3><i class="fas fa-star"></i> 4.8</h3>
                    <p class="mb-0">Calificación Promedio</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Características -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-4 fw-bold">¿Por Qué Elegirnos?</h2>
                <p class="lead text-muted">Razones que nos hacen únicos</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-box text-center">
                        <div class="mb-4">
                            <i class="fas fa-leaf fa-4x text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Ingredientes Frescos</h4>
                        <p class="text-muted">Selección diaria de productos de primera calidad</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-box text-center">
                        <div class="mb-4">
                            <i class="fas fa-concierge-bell fa-4x text-danger"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Servicio Premium</h4>
                        <p class="text-muted">Atención personalizada y profesional</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-box text-center">
                        <div class="mb-4">
                            <i class="fas fa-motorcycle fa-4x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Delivery Rápido</h4>
                        <p class="text-muted">Entrega en 30-45 minutos</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="feature-box text-center">
                        <div class="mb-4">
                            <i class="fas fa-award fa-4x text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Calidad Garantizada</h4>
                        <p class="text-muted">Sabor tradicional en cada plato</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Platos Destacados -->
    <section id="menu" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-4 fw-bold">Platos Destacados</h2>
                <p class="lead text-muted">Lo mejor de nuestra cocina</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($platosDestacados as $plato): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 hover-shadow">
                        <div class="position-relative overflow-hidden">
                            <img src="assets/img/<?php echo htmlspecialchars($plato['imagen']); ?>" 
                                 class="card-img-top" 
                                 style="height: 250px; object-fit: cover;"
                                 alt="<?php echo htmlspecialchars($plato['nombre']); ?>">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-danger">
                                    <i class="fas fa-fire me-1"></i>Popular
                                </span>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold mb-3"><?php echo htmlspecialchars($plato['nombre']); ?></h5>
                            <p class="card-text text-muted small mb-3">
                                <?php echo htmlspecialchars(substr($plato['descripcion'], 0, 80)); ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary"><?php echo ucfirst($plato['categoria']); ?></span>
                                <span class="price fs-4">S/ <?php echo number_format($plato['precio'], 2); ?></span>
                            </div>
                            <a href="vista/pedidos.php" class="btn btn-outline-danger w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Agregar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="vista/menu.php" class="btn btn-danger btn-lg px-5">
                    Ver Menú Completo <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-4 fw-bold">Lo Que Dicen Nuestros Clientes</h2>
                <p class="lead text-muted">Experiencias reales de personas satisfechas</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4">"El mejor ceviche que he probado en Trujillo. Fresco, sabroso y con la cantidad perfecta de limón. ¡Totalmente recomendado!"</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">María García</h6>
                                <small class="text-muted">Cliente frecuente</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4">"Excelente servicio de delivery. Llegó caliente y en el tiempo indicado. El lomo saltado estaba delicioso. Definitivamente volveré a pedir."</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Carlos Mendoza</h6>
                                <small class="text-muted">Cliente satisfecho</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4">"Ambiente acogedor y platos deliciosos. Celebramos el cumpleaños de mi mamá aquí y todo estuvo perfecto. Gracias por la atención."</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Ana Rodríguez</h6>
                                <small class="text-muted">Cliente regular</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container text-center position-relative">
            <h2 class="display-4 fw-bold mb-4">¿Listo para una Experiencia Inolvidable?</h2>
            <p class="lead mb-5">Reserva tu mesa o haz tu pedido ahora y disfruta de nuestros sabores</p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="vista/reservas.php" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-calendar-check me-2"></i>Reservar Mesa
                </a>
                <a href="vista/pedidos.php" class="btn btn-warning btn-lg px-5">
                    <i class="fas fa-shopping-cart me-2"></i>Hacer Pedido
                </a>
                <a href="vista/menu.php" class="btn btn-outline-light btn-lg px-5">
                    <i class="fas fa-book-open me-2"></i>Ver Menú
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scroll para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Animación de entrada para elementos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.card, .feature-box, .testimonial-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>