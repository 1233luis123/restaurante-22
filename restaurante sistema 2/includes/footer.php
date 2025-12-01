<?php
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

<footer class="footer bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <!-- Información del Restaurante -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-section">
                    <h5 class="fw-bold mb-4 text-danger">
                        <i class="fas fa-utensils me-2"></i>Choco's Restaurante
                    </h5>
                    <p class="text-light mb-3">
                        Sabores auténticos de la cocina peruana tradicional. 
                        Experiencia gastronómica única desde 2010.
                    </p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/chocosrestaurante/?locale=es_LA" 
                           target="_blank" 
                           class="btn btn-outline-light btn-sm rounded-circle me-2"
                           style="width: 40px; height: 40px;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://wa.me/943285600" 
                           target="_blank" 
                           class="btn btn-outline-success btn-sm rounded-circle me-2"
                           style="width: 40px; height: 40px;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="mailto:jcarlos2288@live.com" 
                           class="btn btn-outline-danger btn-sm rounded-circle"
                           style="width: 40px; height: 40px;">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Enlaces Rápidos -->
            <div class="col-lg-2 col-md-6">
                <div class="footer-section">
                    <h5 class="fw-bold mb-4">Enlaces</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2">
                            <a href="<?php echo $base; ?>index.php" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2 small"></i>Inicio
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo $vista; ?>menu.php" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2 small"></i>Menú
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo $vista; ?>reservas.php" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2 small"></i>Reservas
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo $vista; ?>contacto.php" class="text-light text-decoration-none">
                                <i class="fas fa-chevron-right me-2 small"></i>Contacto
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Horarios -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-section">
                    <h5 class="fw-bold mb-4">Horarios</h5>
                    <div class="horario-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">
                                <i class="fas fa-calendar-week me-2 text-danger"></i>Lunes - Domingo
                            </span>
                        </div>
                        <p class="ms-4 mb-0 text-light">12:00 PM - 11:00 PM</p>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Abierto todos los días del año</small>
                    </div>
                </div>
            </div>
            
            <!-- Contacto -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-section">
                    <h5 class="fw-bold mb-4">Contacto</h5>
                    <div class="contact-item mb-3">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                        <span class="text-light">
                            Calle 9 de Octubre #513<br>
                            <small class="ms-4">Florencia de Mora, Trujillo</small>
                        </span>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-phone text-danger me-2"></i>
                        <a href="tel:943285600" class="text-light text-decoration-none">
                            943 285 600
                        </a>
                    </div>
                    <div class="contact-item mb-3">
                        <i class="fas fa-envelope text-danger me-2"></i>
                        <a href="mailto:jcarlos2288@live.com" class="text-light text-decoration-none">
                            jcarlos2288@live.com
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-4 bg-secondary">
        
        <!-- Copyright -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <p class="mb-0 text-light">
                    <i class="fas fa-copyright me-1"></i>
                    <?php echo date('Y'); ?> Choco's Restaurante. Todos los derechos reservados.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-light">
                    Desarrollado con <i class="fas fa-heart text-danger mx-1"></i> por 
                    <span class="fw-bold">Grupo 8 UCV</span>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Botón Scroll to Top -->
<button onclick="scrollToTop()" id="scrollTopBtn" class="btn btn-danger rounded-circle shadow" 
        style="position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; display: none; z-index: 999;">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
.footer {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
}

.footer-section h5 {
    position: relative;
    padding-bottom: 10px;
}

.footer-section h5::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background: linear-gradient(to right, #dc3545, transparent);
}

.footer-links li a {
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-links li a:hover {
    color: #dc3545 !important;
    transform: translateX(5px);
}

.social-links a {
    transition: all 0.3s ease;
}

.social-links a:hover {
    transform: translateY(-3px);
}

.contact-item {
    transition: all 0.3s ease;
}

.contact-item:hover {
    transform: translateX(5px);
}

#scrollTopBtn {
    transition: all 0.3s ease;
}

#scrollTopBtn:hover {
    transform: translateY(-5px);
}
</style>

<script>
// Mostrar/Ocultar botón scroll to top
window.onscroll = function() {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        scrollTopBtn.style.display = 'block';
    } else {
        scrollTopBtn.style.display = 'none';
    }
};

// Función scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>