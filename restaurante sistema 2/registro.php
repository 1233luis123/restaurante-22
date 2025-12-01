<?php
session_start();

// Si ya hay sesión activa, redirigir
if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
    header('Location: index.php');
    exit();
}

// Recuperar datos del formulario si hay error
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Choco's Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .registro-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 650px;
            margin: 0 auto;
            animation: slideDown 0.5s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .btn-registro {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-registro:hover {
            background: linear-gradient(45deg, #c82333, #dc3545);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .header-icon {
            background: linear-gradient(45deg, #dc3545, #c82333);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        .password-strength {
            height: 5px;
            border-radius: 3px;
            transition: all 0.3s ease;
            margin-top: 5px;
        }
        .strength-weak { background: #dc3545; width: 33%; }
        .strength-medium { background: #ffc107; width: 66%; }
        .strength-strong { background: #28a745; width: 100%; }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        @media (max-width: 768px) {
            .registro-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="registro-card">
            <div class="text-center mb-4">
                <div class="header-icon">
                    <i class="fas fa-user-plus fa-3x text-white"></i>
                </div>
                <h2 class="fw-bold">Crear Cuenta</h2>
                <p class="text-muted mb-0">Únete a Choco's Restaurante</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Error:</strong> <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="controlador/controladorRegistro.php" method="POST" id="formRegistro" novalidate>
                
                <!-- Nombre Completo -->
                <div class="mb-3">
                    <label for="nombre" class="form-label required-field">
                        <i class="fas fa-user me-2 text-danger"></i>Nombre Completo
                    </label>
                    <input type="text" class="form-control form-control-lg" 
                           id="nombre" name="nombre" required 
                           value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>"
                           placeholder="Ej: Juan Carlos Pérez"
                           minlength="3"
                           maxlength="100">
                    <div class="invalid-feedback">
                        Por favor ingresa tu nombre completo (mínimo 3 caracteres).
                    </div>
                </div>

                <!-- Correo Electrónico -->
                <div class="mb-3">
                    <label for="correo" class="form-label required-field">
                        <i class="fas fa-envelope me-2 text-danger"></i>Correo Electrónico
                    </label>
                    <input type="email" class="form-control form-control-lg" 
                           id="correo" name="correo" required 
                           value="<?php echo htmlspecialchars($form_data['correo'] ?? ''); ?>"
                           placeholder="tucorreo@ejemplo.com"
                           autocomplete="email">
                    <div class="invalid-feedback">
                        Por favor ingresa un correo electrónico válido.
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="mb-3">
                    <label for="telefono" class="form-label">
                        <i class="fas fa-phone me-2 text-danger"></i>Teléfono
                        <small class="text-muted">(opcional)</small>
                    </label>
                    <input type="tel" class="form-control form-control-lg" 
                           id="telefono" name="telefono" 
                           value="<?php echo htmlspecialchars($form_data['telefono'] ?? ''); ?>"
                           placeholder="987654321"
                           pattern="[0-9]{9,15}"
                           maxlength="15">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>Solo números (9-15 dígitos)
                    </small>
                    <div class="invalid-feedback">
                        El teléfono debe contener solo números (9-15 dígitos).
                    </div>
                </div>

                <!-- Contraseñas -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="clave" class="form-label required-field">
                            <i class="fas fa-lock me-2 text-danger"></i>Contraseña
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control form-control-lg" 
                                   id="clave" name="clave" required 
                                   minlength="6" maxlength="50"
                                   placeholder="••••••••"
                                   autocomplete="new-password"
                                   oninput="checkPasswordStrength()">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePassword('clave', 'toggleIcon1')">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <div id="passwordStrength" class="password-strength"></div>
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>Mínimo 6 caracteres
                        </small>
                        <div class="invalid-feedback">
                            La contraseña debe tener al menos 6 caracteres.
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="confirmar_clave" class="form-label required-field">
                            <i class="fas fa-lock me-2 text-danger"></i>Confirmar Contraseña
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control form-control-lg" 
                                   id="confirmar_clave" name="confirmar_clave" required 
                                   minlength="6" maxlength="50"
                                   placeholder="••••••••"
                                   autocomplete="new-password"
                                   oninput="checkPasswordMatch()">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePassword('confirmar_clave', 'toggleIcon2')">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                        <small id="matchMessage" class="text-muted"></small>
                        <div class="invalid-feedback">
                            Las contraseñas no coinciden.
                        </div>
                    </div>
                </div>

                <!-- Términos y Condiciones -->
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="terminos" required>
                        <label class="form-check-label" for="terminos">
                            Acepto los <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#modalTerminos">términos y condiciones</a>
                        </label>
                        <div class="invalid-feedback">
                            Debes aceptar los términos y condiciones.
                        </div>
                    </div>
                </div>

                <!-- Botón de Registro -->
                <button type="submit" class="btn btn-danger btn-registro w-100 mb-3">
                    <i class="fas fa-user-plus me-2"></i>Crear mi Cuenta
                </button>

                <!-- Enlaces -->
                <div class="text-center">
                    <p class="mb-2">¿Ya tienes cuenta? 
                        <a href="login.php" class="text-danger fw-bold text-decoration-none">Inicia sesión</a>
                    </p>
                    <a href="index.php" class="text-muted text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>Volver al inicio
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Términos y Condiciones -->
    <div class="modal fade" id="modalTerminos" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-contract me-2 text-danger"></i>
                        Términos y Condiciones
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Aceptación de los términos</h6>
                    <p class="text-muted">Al registrarte en Choco's Restaurante, aceptas estos términos y condiciones.</p>
                    
                    <h6>2. Uso de la plataforma</h6>
                    <p class="text-muted">Te comprometes a usar la plataforma de manera responsable y legal.</p>
                    
                    <h6>3. Privacidad de datos</h6>
                    <p class="text-muted">Tus datos personales serán protegidos conforme a nuestra política de privacidad.</p>
                    
                    <h6>4. Pedidos y pagos</h6>
                    <p class="text-muted">Los pedidos están sujetos a disponibilidad y confirmación.</p>
                    
                    <h6>5. Modificaciones</h6>
                    <p class="text-muted">Nos reservamos el derecho de modificar estos términos en cualquier momento.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del formulario
        (function() {
            'use strict';
            const form = document.getElementById('formRegistro');
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                // Verificar que las contraseñas coincidan
                const clave = document.getElementById('clave').value;
                const confirmarClave = document.getElementById('confirmar_clave').value;
                
                if (clave !== confirmarClave) {
                    event.preventDefault();
                    event.stopPropagation();
                    document.getElementById('confirmar_clave').setCustomValidity('Las contraseñas no coinciden');
                } else {
                    document.getElementById('confirmar_clave').setCustomValidity('');
                }
                
                form.classList.add('was-validated');
            }, false);
        })();

        // Toggle mostrar/ocultar contraseña
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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

        // Verificar fortaleza de contraseña
        function checkPasswordStrength() {
            const password = document.getElementById('clave').value;
            const strengthBar = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthBar.className = 'password-strength';
                return;
            }
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            if (strength <= 2) {
                strengthBar.className = 'password-strength strength-weak';
            } else if (strength <= 3) {
                strengthBar.className = 'password-strength strength-medium';
            } else {
                strengthBar.className = 'password-strength strength-strong';
            }
        }

        // Verificar que las contraseñas coincidan
        function checkPasswordMatch() {
            const clave = document.getElementById('clave').value;
            const confirmarClave = document.getElementById('confirmar_clave').value;
            const matchMessage = document.getElementById('matchMessage');
            
            if (confirmarClave.length === 0) {
                matchMessage.textContent = '';
                matchMessage.className = 'text-muted';
                return;
            }
            
            if (clave === confirmarClave) {
                matchMessage.innerHTML = '<i class="fas fa-check-circle me-1"></i>Las contraseñas coinciden';
                matchMessage.className = 'text-success';
                document.getElementById('confirmar_clave').setCustomValidity('');
            } else {
                matchMessage.innerHTML = '<i class="fas fa-times-circle me-1"></i>Las contraseñas no coinciden';
                matchMessage.className = 'text-danger';
                document.getElementById('confirmar_clave').setCustomValidity('Las contraseñas no coinciden');
            }
        }

        // Auto-dismiss alerts
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