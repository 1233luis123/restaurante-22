<?php
// Si no existe sesión, iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sesion_activa = isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true;
$usuario_nombre = $_SESSION['usuario_nombre'] ?? '';
$usuario_rol = $_SESSION['usuario_rol'] ?? '';

// Detectar la ruta base según la ubicación del archivo actual
$ruta_actual = $_SERVER['PHP_SELF'];
$es_raiz = (basename(dirname($ruta_actual)) === 'proyecto_choco' || basename(dirname($ruta_actual)) === 'htdocs' || strpos($ruta_actual, '/vista/') === false);
$es_admin = strpos($ruta_actual, '/admin/') !== false;

// Definir rutas base
if ($es_raiz) {
    $base = './';
    $vista = 'vista/';
} elseif ($es_admin) {
    $base = '../../';
    $vista = '../';
} else {
    $base = '../';
    $vista = './';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $base; ?>index.php">
            <i class="fas fa-utensils me-2 fs-4"></i>
            <span class="fw-bold">Choco's Restaurante</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="menuNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="<?php echo $base; ?>index.php">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>" 
                       href="<?php echo $vista; ?>menu.php">
                        <i class="fas fa-book-open me-1"></i>Menú
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reservas.php' ? 'active' : ''; ?>" 
                       href="<?php echo $vista; ?>reservas.php">
                        <i class="fas fa-calendar-check me-1"></i>Reservas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'active' : ''; ?>" 
                       href="<?php echo $vista; ?>contacto.php">
                        <i class="fas fa-envelope me-1"></i>Contacto
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <?php if ($sesion_activa): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                           id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar bg-danger rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                 style="width: 35px; height: 35px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <span><?php echo htmlspecialchars($usuario_nombre); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <?php if ($usuario_rol === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $vista; ?>admin/dashboard.php">
                                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>Panel Administrativo
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo $vista; ?>perfil.php">
                                    <i class="fas fa-user-circle me-2 text-info"></i>Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $vista; ?>pedidos.php">
                                    <i class="fas fa-shopping-bag me-2 text-success"></i>Hacer Pedido
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo $base; ?>logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm me-2" href="<?php echo $base; ?>login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm" href="<?php echo $base; ?>registro.php">
                            <i class="fas fa-user-plus me-1"></i>Registrarse
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar {
    backdrop-filter: blur(10px);
    background-color: rgba(33, 37, 41, 0.95) !important;
}

.navbar-brand {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
}

.nav-link {
    position: relative;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 5px;
}

.nav-link:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545 !important;
}

.nav-link.active {
    background-color: rgba(220, 53, 69, 0.2);
    color: #dc3545 !important;
    font-weight: 600;
}

.dropdown-menu {
    border: none;
    border-radius: 10px;
    padding: 0.5rem;
}

.dropdown-item {
    border-radius: 5px;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.user-avatar {
    font-size: 0.9rem;
}
</style>