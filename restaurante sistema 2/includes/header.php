
<?php
/**
 * Header Global del Sistema
 * Incluye meta tags, CSS y configuraciones globales
 */

// Verificar si hay sesión iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión activa
$sesion_activa = isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true;
$usuario_nombre = $_SESSION['usuario_nombre'] ?? '';
$usuario_rol = $_SESSION['usuario_rol'] ?? '';

// Obtener la URL actual para el menú activo
$ruta_actual = $_SERVER['PHP_SELF'];
$pagina_actual = basename($ruta_actual);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Choco's Restaurante - La mejor gastronomía peruana. Ceviche, Lomo Saltado, Arroz Chaufa y más. Delivery y reservas disponibles.">
    <meta name="keywords" content="restaurante peruano, comida peruana, ceviche, lomo saltado, delivery, reservas, Lima">
    <meta name="author" content="Choco's Restaurante">
    
    <!-- Open Graph Meta Tags (para redes sociales) -->
    <meta property="og:title" content="Choco's Restaurante - Comida Peruana Auténtica">
    <meta property="og:description" content="Disfruta de la mejor gastronomía peruana en un ambiente acogedor">
    <meta property="og:image" content="<?php echo URL_BASE ?? ''; ?>assets/img/logo.jpg">
    <meta property="og:type" content="website">
    
    <!-- Título dinámico -->
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina . ' - ' : ''; ?>Choco's Restaurante</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo URL_BASE ?? '../'; ?>assets/img/favicon.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo URL_BASE ?? '../'; ?>assets/css/estilo.css">
    
    <!-- Estilos adicionales específicos de la página (si existen) -->
    <?php if (isset($estilos_adicionales)): ?>
        <?php foreach ($estilos_adicionales as $estilo): ?>
            <link rel="stylesheet" href="<?php echo $estilo; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- CSS Inline para optimización -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        
        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        
        .preloader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .preloader-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #dc3545;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <!-- Scripts adicionales específicos de la página en head (si existen) -->
    <?php if (isset($scripts_head)): ?>
        <?php foreach ($scripts_head as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-spinner"></div>
    </div>
    
    <!-- Data attributes para JavaScript (información de sesión) -->
    <?php if ($sesion_activa): ?>
    <div id="sesion-data" style="display:none;" 
         data-usuario-nombre="<?php echo htmlspecialchars($usuario_nombre); ?>"
         data-usuario-correo="<?php echo htmlspecialchars($_SESSION['usuario_correo'] ?? ''); ?>"
         data-usuario-telefono="<?php echo htmlspecialchars($_SESSION['usuario_telefono'] ?? ''); ?>"
         data-usuario-rol="<?php echo htmlspecialchars($usuario_rol); ?>">
    </div>
    <?php endif; ?>
    
    <!-- Script para ocultar preloader -->
    <script>
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                setTimeout(() => {
                    preloader.classList.add('hidden');
                    setTimeout(() => preloader.remove(), 300);
                }, 500);
            }
        });
    </script>