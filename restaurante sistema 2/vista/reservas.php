<?php
session_start();
require_once '../config/conexion.php';

// Verificar si hay sesión activa
$sesion_activa = isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true;

// Si hay sesión, obtener las reservas del usuario
$misReservas = [];
if ($sesion_activa) {
    require_once '../modelo/Reserva.php';
    $modeloReserva = new Reserva();
    $misReservas = $modeloReserva->obtenerPorUsuario($_SESSION['usuario_id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Choco's Restaurante</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 70px;
        }
        
        .hero-reservas {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-reservas::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><path d="M0 0 L50 50 L0 100 L50 50 L100 100 L100 0 Z" fill="rgba(255,255,255,0.05)"/></svg>');
            background-size: 100px 100px;
            animation: move 20s linear infinite;
        }
        
        @keyframes move {
            0% { transform: translate(0, 0); }
            100% { transform: translate(100px, 100px); }
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .form-reserva {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.8);
        }
        
        .form-reserva:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0,0,0,0.2);
        }
        
        .form-reserva h3, .form-reserva h4 {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        
        .form-reserva h3::after, .form-reserva h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #dc3545, #ff6b6b);
            border-radius: 2px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 12px 20px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
            transform: translateY(-2px);
        }
        
        .form-label {
            color: #495057;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .btn-reservar {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-reservar::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-reservar:hover::before {
            left: 100%;
        }
        
        .btn-reservar:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(220, 53, 69, 0.5);
        }
        
        .info-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid;
            transition: all 0.3s ease;
        }
        
        .info-box.success {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        
        .info-box:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .reserva-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #dc3545;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .reserva-item:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .reserva-item.confirmada {
            border-left-color: #28a745;
        }
        
        .reserva-item.pendiente {
            border-left-color: #ffc107;
        }
        
        .reserva-item.cancelada {
            border-left-color: #dc3545;
            opacity: 0.7;
        }
        
        .badge-estado {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-top: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .stat-card.primary {
            border-top-color: #667eea;
        }
        
        .stat-card.success {
            border-top-color: #28a745;
        }
        
        .stat-card.warning {
            border-top-color: #ffc107;
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2rem;
        }
        
        .stat-icon.primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .stat-icon.success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .stat-icon.warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 40px;
            padding-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: -20px;
            width: 2px;
            background: #dee2e6;
        }
        
        .timeline-item:last-child::before {
            display: none;
        }
        
        .timeline-dot {
            position: absolute;
            left: 8px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #dc3545;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #dee2e6;
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-reservas text-white">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="floating">
                        <i class="fas fa-calendar-alt fa-5x mb-4"></i>
                    </div>
                    <h1 class="display-3 fw-bold mb-4">Reserva tu Mesa</h1>
                    <p class="lead fs-4 mb-4">
                        Asegura tu lugar en nuestro restaurante y disfruta de una experiencia gastronómica única
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="badge bg-white text-dark px-4 py-2 fs-6">
                            <i class="fas fa-clock text-success me-2"></i>Confirmación Inmediata
                        </div>
                        <div class="badge bg-white text-dark px-4 py-2 fs-6">
                            <i class="fas fa-star text-warning me-2"></i>Mejor Atención
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card primary">
                                <div class="stat-icon primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="fw-bold mb-0">1-10</h3>
                                <p class="text-muted mb-0">Personas</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card success">
                                <div class="stat-icon success">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3 class="fw-bold mb-0">12-11 PM</h3>
                                <p class="text-muted mb-0">Horario</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-5">
        <div class="container">
            
            <!-- Mensajes -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle fs-3 me-3"></i>
                        <div>
                            <strong>¡Error!</strong>
                            <p class="mb-0"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['exito'])): ?>
                <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fs-3 me-3"></i>
                        <div>
                            <strong>¡Éxito!</strong>
                            <p class="mb-0"><?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row g-4">
                <!-- Formulario de Reserva -->
                <div class="col-lg-7">
                    <div class="form-reserva">
                        <h3>
                            <i class="fas fa-calendar-check text-danger me-2"></i>
                            Nueva Reserva
                        </h3>

                        <form action="../controlador/controladorReserva.php" method="POST" id="formReserva">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="fas fa-user text-danger me-2"></i>Nombre Completo *
                                    </label>
                                    <input type="text" class="form-control" name="nombre" required
                                           value="<?php echo $sesion_activa ? htmlspecialchars($_SESSION['usuario_nombre']) : ''; ?>"
                                           placeholder="Ej: Juan Pérez">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="fas fa-phone text-danger me-2"></i>Teléfono *
                                    </label>
                                    <input type="tel" class="form-control" name="telefono" required
                                           value="<?php echo $sesion_activa ? htmlspecialchars($_SESSION['usuario_telefono'] ?? '') : ''; ?>"
                                           placeholder="987654321" pattern="[0-9]{9}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="fas fa-envelope text-danger me-2"></i>Correo Electrónico *
                                    </label>
                                    <input type="email" class="form-control" name="correo" required
                                           value="<?php echo $sesion_activa ? htmlspecialchars($_SESSION['usuario_correo']) : ''; ?>"
                                           placeholder="tucorreo@ejemplo.com">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-calendar text-danger me-2"></i>Fecha *
                                    </label>
                                    <input type="date" class="form-control" name="fecha" required
                                           min="<?php echo date('Y-m-d'); ?>" id="fechaReserva">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-clock text-danger me-2"></i>Hora *
                                    </label>
                                    <select class="form-select" name="hora" required>
                                        <option value="">Seleccionar...</option>
                                        <optgroup label="Almuerzo">
                                            <option value="12:00">12:00 PM</option>
                                            <option value="12:30">12:30 PM</option>
                                            <option value="13:00">1:00 PM</option>
                                            <option value="13:30">1:30 PM</option>
                                            <option value="14:00">2:00 PM</option>
                                        </optgroup>
                                        <optgroup label="Cena">
                                            <option value="19:00">7:00 PM</option>
                                            <option value="19:30">7:30 PM</option>
                                            <option value="20:00">8:00 PM</option>
                                            <option value="20:30">8:30 PM</option>
                                            <option value="21:00">9:00 PM</option>
                                            <option value="21:30">9:30 PM</option>
                                            <option value="22:00">10:00 PM</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-users text-danger me-2"></i>Personas *
                                    </label>
                                    <select class="form-select" name="personas" required>
                                        <option value="">Seleccionar...</option>
                                        <?php for($i = 1; $i <= 9; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> persona<?php echo $i > 1 ? 's' : ''; ?></option>
                                        <?php endfor; ?>
                                        <option value="10">10+ personas</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="fas fa-comment text-danger me-2"></i>Mensaje (Opcional)
                                    </label>
                                    <textarea class="form-control" name="mensaje" rows="4"
                                              placeholder="Ej: Celebración de cumpleaños, alergias alimentarias, ubicación preferida..."></textarea>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Cuéntanos si hay algo especial que debamos saber
                                    </small>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger btn-reservar w-100">
                                        <i class="fas fa-calendar-check me-2"></i>Confirmar Reserva
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Información y Mis Reservas -->
                <div class="col-lg-5">
                    
                    <!-- Información Importante -->
                    <div class="form-reserva mb-4">
                        <h4>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Información Importante
                        </h4>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="info-box success">
                                <strong><i class="fas fa-clock me-2"></i>Horario</strong>
                                <p class="mb-0 mt-1">Lunes a Domingo: 12:00 PM - 11:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="info-box success">
                                <strong><i class="fas fa-check-circle me-2"></i>Confirmación</strong>
                                <p class="mb-0 mt-1">Recibirás un email de confirmación inmediato</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="info-box success">
                                <strong><i class="fas fa-ban me-2"></i>Cancelación</strong>
                                <p class="mb-0 mt-1">Puedes cancelar hasta 2 horas antes sin costo</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="info-box success">
                                <strong><i class="fas fa-hourglass-half me-2"></i>Tolerancia</strong>
                                <p class="mb-0 mt-1">15 minutos de espera máximo por tu mesa</p>
                            </div>
                        </div>

                        <div class="alert alert-warning alert-custom mt-3">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Nota:</strong>
                            Para grupos de más de 10 personas, contacta al 
                            <a href="tel:943285600" class="alert-link fw-bold">943 285 600</a>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="tel:943285600" class="btn btn-outline-danger">
                                <i class="fas fa-phone me-2"></i>Llamar Ahora
                            </a>
                            <a href="https://wa.me/943285600" target="_blank" class="btn btn-outline-success">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        </div>
                    </div>

                    <!-- Mis Reservas -->
                    <?php if ($sesion_activa): ?>
                    <div class="form-reserva">
                        <h4>
                            <i class="fas fa-history text-info me-2"></i>
                            Mis Reservas Recientes
                        </h4>

                        <?php if (empty($misReservas)): ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h5 class="text-muted">No tienes reservas</h5>
                                <p class="text-muted small">Haz tu primera reserva y aparecerá aquí</p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($misReservas, 0, 3) as $reserva): ?>
                            <div class="reserva-item <?php echo $reserva['estado']; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold">
                                            <i class="fas fa-hashtag me-1"></i>
                                            Reserva #<?php echo $reserva['id_reserva']; ?>
                                        </h6>
                                    </div>
                                    <span class="badge badge-estado bg-<?php 
                                        echo $reserva['estado'] === 'confirmada' ? 'success' : 
                                            ($reserva['estado'] === 'pendiente' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($reserva['estado']); ?>
                                    </span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-calendar text-danger me-1"></i> Fecha
                                        </small>
                                        <strong><?php echo date('d/m/Y', strtotime($reserva['fecha'])); ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-clock text-danger me-1"></i> Hora
                                        </small>
                                        <strong><?php echo date('h:i A', strtotime($reserva['hora'])); ?></strong>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-users text-danger me-1"></i> Personas
                                        </small>
                                        <strong><?php echo $reserva['personas']; ?> persona<?php echo $reserva['personas'] > 1 ? 's' : ''; ?></strong>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <?php if (count($misReservas) > 3): ?>
                            <div class="text-center mt-3">
                                <a href="perfil.php" class="btn btn-outline-primary w-100">
                                    Ver todas mis reservas
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="form-reserva text-center">
                        <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                        <h5>Inicia Sesión</h5>
                        <p class="text-muted mb-4">Para ver tu historial de reservas</p>
                        <a href="../login.php" class="btn btn-danger">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        document.getElementById('formReserva').addEventListener('submit', function(e) {
            const telefono = this.querySelector('[name="telefono"]').value;
            if (!/^[0-9]{9}$/.test(telefono)) {
                e.preventDefault();
                alert('El teléfono debe tener exactamente 9 dígitos');
                return false;
            }
        });
        
        // Establecer fecha mínima (hoy)
        const fechaInput = document.getElementById('fechaReserva');
        fechaInput.min = new Date().toISOString().split('T')[0];
        
        // Animación de entrada
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        entry.target.style.transition = 'all 0.5s ease';
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        document.querySelectorAll('.form-reserva, .reserva-item').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>