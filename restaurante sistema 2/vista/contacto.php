<?php
session_start();

// Recuperar datos del formulario si hay error
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Choco's Restaurante</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    
    <style>
        .contacto-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 150px 0 80px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .contacto-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/img/pattern.png');
            opacity: 0.1;
        }
        
        .form-contacto {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .form-contacto:hover {
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }
        
        .info-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            border-left-color: #dc3545;
        }
        
        .info-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .info-card:hover .info-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .mapa-container {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .mapa-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), transparent);
            pointer-events: none;
            z-index: 1;
        }
        
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            border: 2px solid;
        }
        
        .social-btn:hover {
            transform: translateY(-5px) scale(1.1);
        }
        
        .social-btn.facebook {
            border-color: #1877f2;
            color: #1877f2;
        }
        
        .social-btn.facebook:hover {
            background: #1877f2;
            color: white;
        }
        
        .social-btn.whatsapp {
            border-color: #25d366;
            color: #25d366;
        }
        
        .social-btn.whatsapp:hover {
            background: #25d366;
            color: white;
        }
        
        .social-btn.email {
            border-color: #dc3545;
            color: #dc3545;
        }
        
        .social-btn.email:hover {
            background: #dc3545;
            color: white;
        }
        
        .horario-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            display: inline-block;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.3);
        }
        
        .contact-info-list {
            list-style: none;
            padding: 0;
        }
        
        .contact-info-list li {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }
        
        .contact-info-list li:last-child {
            border-bottom: none;
        }
        
        .contact-info-list li:hover {
            padding-left: 10px;
            color: #dc3545;
        }
        
        .form-control, .form-select {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
            transform: translateY(-2px);
        }
        
        .btn-enviar {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-enviar:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(220, 53, 69, 0.4);
        }
        
        .stats-mini {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }
        
        .stats-mini h4 {
            font-size: 2rem;
            font-weight: 800;
            color: #dc3545;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Header -->
    <section class="contacto-header">
        <div class="container text-center position-relative">
            <h1 class="display-3 fw-bold mb-4">
                <i class="fas fa-envelope me-3"></i>Contáctanos
            </h1>
            <p class="lead fs-4">Estamos aquí para atenderte y responder todas tus consultas</p>
            <div class="mt-4">
                <span class="badge bg-white text-dark fs-6 px-4 py-2 me-2">
                    <i class="fas fa-clock text-success me-2"></i>Respuesta en 24 horas
                </span>
                <span class="badge bg-white text-dark fs-6 px-4 py-2">
                    <i class="fas fa-headset text-danger me-2"></i>Atención Personalizada
                </span>
            </div>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-5">
        <div class="container">
            
            <!-- Mensajes -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>¡Error!</strong> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['exito'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>¡Éxito!</strong> <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row g-4">
                <!-- Formulario de Contacto -->
                <div class="col-lg-7">
                    <div class="form-contacto">
                        <div class="mb-4">
                            <h3 class="fw-bold mb-2">
                                <i class="fas fa-paper-plane text-danger me-2"></i>
                                Envíanos un Mensaje
                            </h3>
                            <p class="text-muted">Completa el formulario y nos pondremos en contacto contigo</p>
                        </div>

                        <form action="../controlador/controladorContacto.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-user me-2 text-danger"></i>Nombre Completo *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="nombre" 
                                           required
                                           value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>"
                                           placeholder="Ej: Juan Pérez">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-phone me-2 text-danger"></i>Teléfono
                                    </label>
                                    <input type="tel" 
                                           class="form-control" 
                                           name="telefono"
                                           value="<?php echo htmlspecialchars($form_data['telefono'] ?? ''); ?>"
                                           placeholder="987654321">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2 text-danger"></i>Correo Electrónico *
                                    </label>
                                    <input type="email" 
                                           class="form-control" 
                                           name="correo" 
                                           required
                                           value="<?php echo htmlspecialchars($form_data['correo'] ?? ''); ?>"
                                           placeholder="tucorreo@ejemplo.com">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-comment me-2 text-danger"></i>Mensaje *
                                    </label>
                                    <textarea class="form-control" 
                                              name="mensaje" 
                                              rows="6" 
                                              required
                                              placeholder="Escribe tu mensaje aquí. Mínimo 10 caracteres..."><?php echo htmlspecialchars($form_data['mensaje'] ?? ''); ?></textarea>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Mínimo 10 caracteres
                                    </small>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger btn-enviar w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4 pt-4 border-top">
                            <p class="text-muted mb-3">O contáctanos directamente por:</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="https://www.facebook.com/chocosrestaurante/?locale=es_LA" 
                                   target="_blank" 
                                   class="social-btn facebook"
                                   title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://wa.me/943285600" 
                                   target="_blank" 
                                   class="social-btn whatsapp"
                                   title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="mailto:jcarlos2288@live.com" 
                                   class="social-btn email"
                                   title="Email">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="col-lg-5">
                    
                    <!-- Ubicación -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Ubicación</h5>
                        <ul class="contact-info-list">
                            <li>
                                <i class="fas fa-map-marked-alt text-danger me-2"></i>
                                <strong>Dirección:</strong><br>
                                Calle 9 de Octubre #513<br>
                                Florencia de Mora, Trujillo
                            </li>
                        </ul>
                    </div>

                    <!-- Teléfono -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Teléfono</h5>
                        <ul class="contact-info-list">
                            <li>
                                <i class="fas fa-mobile-alt text-danger me-2"></i>
                                <a href="tel:943285600" class="text-decoration-none text-dark fw-bold fs-5">
                                    (51) 943 285 600
                                </a>
                            </li>
                            <li>
                                <i class="fab fa-whatsapp text-success me-2"></i>
                                <a href="https://wa.me/943285600" target="_blank" class="text-decoration-none text-dark">
                                    WhatsApp Business
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Email -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Correo Electrónico</h5>
                        <ul class="contact-info-list">
                            <li>
                                <i class="fas fa-at text-danger me-2"></i>
                                <a href="mailto:jcarlos2288@live.com" class="text-decoration-none text-dark">
                                    jcarlos2288@live.com
                                </a>
                            </li>
                            <li class="text-muted small">
                                <i class="fas fa-clock me-2"></i>
                                Respuesta en 24 horas
                            </li>
                        </ul>
                    </div>

                    <!-- Horario -->
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Horario de Atención</h5>
                        <div class="text-center py-3">
                            <div class="horario-badge mb-3">
                                <i class="fas fa-calendar-check me-2"></i>
                                Lunes a Domingo
                            </div>
                            <h4 class="fw-bold text-danger">12:00 PM - 11:00 PM</h4>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Abierto todos los días del año
                            </p>
                        </div>
                    </div>

                    <!-- Mini Stats -->
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-mini">
                                <h4>30-45</h4>
                                <small class="text-muted">min. delivery</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-mini">
                                <h4>24/7</h4>
                                <small class="text-muted">Pedidos online</small>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <!-- Mapa -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-5 fw-bold">
                    <i class="fas fa-map-marked-alt text-danger me-3"></i>
                    Encuéntranos Aquí
                </h2>
                <p class="lead text-muted">Visítanos en nuestra ubicación</p>
            </div>

            <div class="mapa-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.290648141657!2d-79.0278!3d-8.0833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91ad160213f83c71%3A0xe2ca02e3a138fb33!2sCalle%209%20de%20Octubre%20513%2C%20Choco's%20Restaurante!5e0!3m2!1ses-419!2spe!4v1731187200000!5m2!1ses-419!2spe"
                    width="100%"
                    height="500"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            
            <div class="text-center mt-4">
                <a href="https://www.google.com/maps/dir/?api=1&destination=Calle+9+de+Octubre+513+Florencia+de+Mora+Trujillo" 
                   target="_blank" 
                   class="btn btn-danger btn-lg">
                    <i class="fas fa-directions me-2"></i>Cómo Llegar
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-5 fw-bold">Preguntas Frecuentes</h2>
                <p class="lead text-muted">Respuestas a las consultas más comunes</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 mb-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="fas fa-motorcycle text-danger me-3"></i>
                                    ¿Cuál es el tiempo de entrega del delivery?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    El tiempo estimado de entrega es de 30 a 45 minutos, dependiendo de tu ubicación. 
                                    Hacemos todo lo posible por entregar tu pedido caliente y en el menor tiempo posible.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="fas fa-credit-card text-danger me-3"></i>
                                    ¿Qué métodos de pago aceptan?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Aceptamos efectivo, tarjetas de crédito/débito, Yape, Plin y transferencias bancarias. 
                                    Puedes elegir tu método de pago al momento de realizar tu pedido.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="fas fa-calendar-check text-danger me-3"></i>
                                    ¿Necesito reservar con anticipación?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Para grupos grandes (más de 6 personas) recomendamos reservar con al menos 24 horas de anticipación. 
                                    Para grupos pequeños, puedes reservar el mismo día.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    <i class="fas fa-leaf text-danger me-3"></i>
                                    ¿Tienen opciones vegetarianas?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sí, contamos con opciones vegetarianas en nuestro menú. 
                                    También podemos adaptar algunos platos según tus preferencias alimentarias.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        
        document.querySelectorAll('.info-card, .form-contacto').forEach(el => {
            observer.observe(el);
        });
        
        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const mensaje = this.querySelector('[name="mensaje"]').value;
            if (mensaje.length < 10) {
                e.preventDefault();
                alert('El mensaje debe tener al menos 10 caracteres');
            }
        });
    </script>
</body>
</html>