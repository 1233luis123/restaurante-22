<?php
session_start();

// Si ya hay sesión activa, redirigir
if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
    if ($_SESSION['usuario_rol'] === 'admin') {
        header('Location: vista/admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Choco's Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .login-image {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), 
                        url('assets/img/ceviche.jpg') center/cover;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .login-image-content {
            text-align: center;
            padding: 20px;
        }
        .login-image h3 {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .login-form {
            padding: 50px 40px;
        }
        .btn-login {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: linear-gradient(45deg, #c82333, #dc3545);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .usuarios-prueba {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #dc3545;
        }
        @media (max-width: 768px) {
            .login-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="login-card row g-0">
                    <!-- Imagen lateral -->
                    <div class="col-md-6 d-none d-md-flex login-image">
                        <div class="login-image-content">
                            <i class="fas fa-utensils fa-4x mb-3"></i>
                            <h3>Choco's Restaurante</h3>
                            <p class="lead">La mejor comida peruana de la ciudad</p>
                        </div>
                    </div>
                    
                    <!-- Formulario -->
                    <div class="col-md-6">
                        <div class="login-form">
                            <div class="text-center mb-4">
                                <i class="fas fa-utensils fa-3x text-danger mb-3 d-md-none"></i>
                                <h2 class="fw-bold">Bienvenido</h2>
                                <p class="text-muted">Inicia sesión en tu cuenta</p>
                            </div>

                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php 
                                    echo htmlspecialchars($_SESSION['error']); 
                                    unset($_SESSION['error']);
                                    ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['exito'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php 
                                    echo htmlspecialchars($_SESSION['exito']); 
                                    unset($_SESSION['exito']);
                                    ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form action="controlador/controladorLogin.php" method="POST" id="formLogin">
                                <div class="mb-3">
                                    <label for="correo" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2 text-danger"></i>Correo Electrónico
                                    </label>
                                    <input type="email" class="form-control form-control-lg" 
                                           id="correo" name="correo" required 
                                           placeholder="tucorreo@ejemplo.com"
                                           autocomplete="email">
                                </div>

                                <div class="mb-3">
                                    <label for="clave" class="form-label fw-bold">
                                        <i class="fas fa-lock me-2 text-danger"></i>Contraseña
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-lg" 
                                               id="clave" name="clave" required 
                                               placeholder="••••••••"
                                               autocomplete="current-password">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="togglePassword()">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="recordar">
                                    <label class="form-check-label" for="recordar">
                                        Recordarme
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-danger btn-login w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </button>

                                <div class="text-center">
                                    <p class="mb-2">¿No tienes cuenta? 
                                        <a href="registro.php" class="text-danger fw-bold text-decoration-none">Regístrate aquí</a>
                                    </p>
                                    <a href="index.php" class="text-muted text-decoration-none">
                                        <i class="fas fa-arrow-left me-1"></i>Volver al inicio
                                    </a>
                                </div>
                            </form>

                            <!-- Usuarios de prueba (OCULTAR EN PRODUCCIÓN) -->
                            <div class="mt-4 p-3 usuarios-prueba rounded">
                                <small class="text-muted d-block mb-2 fw-bold">
                                    <i class="fas fa-info-circle me-1"></i>Usuarios de prueba:
                                </small>
                                <small class="text-muted d-block mb-1">
                                    👤 <strong>Admin:</strong> admin@chocos.com / admin123
                                </small>
                                <small class="text-muted d-block">
                                    👤 <strong>Cliente:</strong> cliente@test.com / cliente123
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle para mostrar/ocultar contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('clave');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Auto-dismiss alerts después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>