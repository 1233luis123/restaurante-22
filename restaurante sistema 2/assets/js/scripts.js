
/**
 * ============================================
 * CHOCO'S RESTAURANTE - SISTEMA COMPLETO
 * JavaScript para todas las funcionalidades
 * ============================================
 */

// ============================================
// INICIALIZACIÓN GLOBAL
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🍽️ Sistema Choco\'s Restaurante cargado');
    
    // Inicializar módulos
    inicializarNavbar();
    inicializarScrollTop();
    inicializarAnimaciones();
    inicializarFormularioReserva();
    inicializarFormularioPedido();
    inicializarFormularioContacto();
    inicializarCarrito();
    autoOcultarAlertas();
});

// ============================================
// NAVBAR - SCROLL EFFECT
// ============================================
function inicializarNavbar() {
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
                navbar.style.padding = '10px 0';
            } else {
                navbar.classList.remove('navbar-scrolled');
                navbar.style.padding = '15px 0';
            }
        });
    }
    
    // Cerrar menú móvil al hacer clic en un enlace
    const navLinks = document.querySelectorAll('.nav-link');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                bsCollapse.hide();
            }
        });
    });
}

// ============================================
// SCROLL TO TOP BUTTON
// ============================================
function inicializarScrollTop() {
    let scrollTopBtn = document.getElementById('scrollTopBtn');
    
    // Crear botón si no existe
    if (!scrollTopBtn) {
        scrollTopBtn = document.createElement('button');
        scrollTopBtn.id = 'scrollTopBtn';
        scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        scrollTopBtn.setAttribute('aria-label', 'Volver arriba');
        document.body.appendChild(scrollTopBtn);
    }
    
    // Mostrar/ocultar botón según scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollTopBtn.classList.add('show');
        } else {
            scrollTopBtn.classList.remove('show');
        }
    });
    
    // Acción del botón
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ============================================
// ANIMACIONES AL SCROLL
// ============================================
function inicializarAnimaciones() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observar elementos
    const elementos = document.querySelectorAll('.card, .contacto-item, .pedido-card');
    elementos.forEach(el => observer.observe(el));
}

// ============================================
// FORMULARIO DE RESERVAS
// ============================================
function inicializarFormularioReserva() {
    const formReserva = document.getElementById('formReserva');
    
    if (!formReserva) return;
    
    const inputFecha = document.getElementById('fecha_reserva');
    const inputHora = document.getElementById('hora_reserva');
    const inputPersonas = document.getElementById('num_personas');
    
    // Configurar fecha mínima (hoy)
    if (inputFecha) {
        const hoy = new Date().toISOString().split('T')[0];
        inputFecha.setAttribute('min', hoy);
        
        // Fecha máxima (3 meses adelante)
        const maxFecha = new Date();
        maxFecha.setMonth(maxFecha.getMonth() + 3);
        inputFecha.setAttribute('max', maxFecha.toISOString().split('T')[0]);
        
        inputFecha.addEventListener('change', validarHoraSegunFecha);
    }
    
    // Validar hora
    if (inputHora) {
        inputHora.addEventListener('change', validarHoraDisponible);
    }
    
    // Validar número de personas
    if (inputPersonas) {
        inputPersonas.addEventListener('input', function() {
            const valor = parseInt(this.value);
            if (valor < 1) this.value = 1;
            if (valor > 20) this.value = 20;
        });
    }
    
    // Validación del formulario
    formReserva.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validarFormularioReserva()) {
            mostrarConfirmacionReserva(formReserva);
        }
    });
    
    // Auto-rellenar si el usuario está logueado
    autoRellenarDatosUsuario();
}

function validarHoraSegunFecha() {
    const inputFecha = document.getElementById('fecha_reserva');
    const inputHora = document.getElementById('hora_reserva');
    
    if (!inputFecha || !inputHora) return;
    
    const fechaSeleccionada = new Date(inputFecha.value + 'T00:00:00');
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    if (fechaSeleccionada.getTime() === hoy.getTime()) {
        const horaActual = new Date().getHours();
        inputHora.setAttribute('min', `${horaActual + 2}:00`);
        mostrarAlerta('info', 'Para reservas de hoy, debe ser al menos 2 horas después');
    } else {
        inputHora.setAttribute('min', '11:00');
    }
}

function validarHoraDisponible() {
    const inputHora = document.getElementById('hora_reserva');
    if (!inputHora || !inputHora.value) return;
    
    const [horas, minutos] = inputHora.value.split(':').map(Number);
    const horaEnMinutos = horas * 60 + minutos;
    
    const almuerzoInicio = 11 * 60;
    const almuerzoFin = 16 * 60;
    const cenaInicio = 18 * 60;
    const cenaFin = 23 * 60;
    
    if ((horaEnMinutos >= almuerzoInicio && horaEnMinutos <= almuerzoFin) ||
        (horaEnMinutos >= cenaInicio && horaEnMinutos <= cenaFin)) {
        inputHora.classList.remove('is-invalid');
        inputHora.classList.add('is-valid');
    } else {
        inputHora.classList.remove('is-valid');
        inputHora.classList.add('is-invalid');
        mostrarAlerta('warning', 'Horarios: 11:00-16:00 y 18:00-23:00');
    }
}

function validarFormularioReserva() {
    let esValido = true;
    const errores = [];
    
    // Validar nombre
    const nombre = document.getElementById('nombre_reserva');
    if (nombre && nombre.value.trim().length < 3) {
        errores.push('El nombre debe tener al menos 3 caracteres');
        nombre.classList.add('is-invalid');
        esValido = false;
    } else if (nombre) {
        nombre.classList.remove('is-invalid');
        nombre.classList.add('is-valid');
    }
    
    // Validar teléfono
    const telefono = document.getElementById('telefono_reserva');
    const telefonoRegex = /^[0-9]{9}$/;
    if (telefono && !telefonoRegex.test(telefono.value.trim())) {
        errores.push('El teléfono debe tener 9 dígitos');
        telefono.classList.add('is-invalid');
        esValido = false;
    } else if (telefono) {
        telefono.classList.remove('is-invalid');
        telefono.classList.add('is-valid');
    }
    
    // Validar email
    const email = document.getElementById('correo_reserva');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email.value.trim())) {
        errores.push('Ingrese un correo válido');
        email.classList.add('is-invalid');
        esValido = false;
    } else if (email) {
        email.classList.remove('is-invalid');
        email.classList.add('is-valid');
    }
    
    // Validar fecha
    const fecha = document.getElementById('fecha_reserva');
    if (fecha && !fecha.value) {
        errores.push('Seleccione una fecha');
        fecha.classList.add('is-invalid');
        esValido = false;
    } else if (fecha) {
        fecha.classList.remove('is-invalid');
        fecha.classList.add('is-valid');
    }
    
    // Validar hora
    const hora = document.getElementById('hora_reserva');
    if (hora && !hora.value) {
        errores.push('Seleccione una hora');
        hora.classList.add('is-invalid');
        esValido = false;
    }
    
    // Validar personas
    const personas = document.getElementById('num_personas');
    if (personas) {
        const valor = parseInt(personas.value);
        if (valor < 1 || valor > 20) {
            errores.push('Número de personas: 1-20');
            personas.classList.add('is-invalid');
            esValido = false;
        } else {
            personas.classList.remove('is-invalid');
            personas.classList.add('is-valid');
        }
    }
    
    if (errores.length > 0) {
        mostrarAlerta('danger', errores.join('<br>'));
    }
    
    return esValido;
}

function mostrarConfirmacionReserva(form) {
    const nombre = document.getElementById('nombre_reserva').value;
    const fecha = document.getElementById('fecha_reserva').value;
    const hora = document.getElementById('hora_reserva').value;
    const personas = document.getElementById('num_personas').value;
    
    const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-PE', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const mensaje = `
        <div class="text-start">
            <p class="mb-2"><strong>Nombre:</strong> ${nombre}</p>
            <p class="mb-2"><strong>Fecha:</strong> ${fechaFormateada}</p>
            <p class="mb-2"><strong>Hora:</strong> ${hora}</p>
            <p class="mb-2"><strong>Personas:</strong> ${personas}</p>
        </div>
        <p class="mt-3 text-muted small">¿Confirma los datos?</p>
    `;
    
    mostrarModal('Confirmar Reserva', mensaje, function() {
        form.submit();
    });
}

// ============================================
// FORMULARIO DE PEDIDOS / CARRITO
// ============================================
function inicializarFormularioPedido() {
    const botonesAgregar = document.querySelectorAll('.btn-agregar-carrito');
    
    botonesAgregar.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platoId = this.dataset.platoId;
            const platoNombre = this.dataset.platoNombre;
            const platoPrecio = parseFloat(this.dataset.platoPrecio);
            
            agregarAlCarrito(platoId, platoNombre, platoPrecio);
        });
    });
}

function inicializarCarrito() {
    // Cargar carrito desde sessionStorage
    cargarCarritoDesdeStorage();
    
    // Actualizar contador del carrito
    actualizarContadorCarrito();
}

let carrito = [];

function agregarAlCarrito(id, nombre, precio) {
    const item = carrito.find(i => i.id === id);
    
    if (item) {
        item.cantidad++;
    } else {
        carrito.push({ id, nombre, precio, cantidad: 1 });
    }
    
    guardarCarritoEnStorage();
    actualizarContadorCarrito();
    mostrarAlerta('success', `${nombre} agregado al carrito`);
}

function eliminarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    guardarCarritoEnStorage();
    actualizarContadorCarrito();
    renderizarCarrito();
}

function actualizarCantidad(id, cantidad) {
    const item = carrito.find(i => i.id === id);
    if (item) {
        item.cantidad = Math.max(1, parseInt(cantidad));
        guardarCarritoEnStorage();
        renderizarCarrito();
    }
}

function guardarCarritoEnStorage() {
    sessionStorage.setItem('carrito', JSON.stringify(carrito));
}

function cargarCarritoDesdeStorage() {
    const carritoGuardado = sessionStorage.getItem('carrito');
    if (carritoGuardado) {
        carrito = JSON.parse(carritoGuardado);
    }
}

function actualizarContadorCarrito() {
    const contador = document.getElementById('contadorCarrito');
    if (contador) {
        const total = carrito.reduce((sum, item) => sum + item.cantidad, 0);
        contador.textContent = total;
        contador.style.display = total > 0 ? 'inline-block' : 'none';
    }
}

function renderizarCarrito() {
    const contenedorCarrito = document.getElementById('itemsCarrito');
    const totalCarrito = document.getElementById('totalCarrito');
    
    if (!contenedorCarrito) return;
    
    if (carrito.length === 0) {
        contenedorCarrito.innerHTML = '<p class="text-center text-muted">El carrito está vacío</p>';
        if (totalCarrito) totalCarrito.textContent = 'S/ 0.00';
        return;
    }
    
    let html = '';
    let total = 0;
    
    carrito.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        
        html += `
            <div class="carrito-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>${item.nombre}</strong>
                    <button class="btn btn-sm btn-danger" onclick="eliminarDelCarrito('${item.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="input-group" style="width: 120px;">
                        <button class="btn btn-sm btn-outline-secondary" onclick="actualizarCantidad('${item.id}', ${item.cantidad - 1})">-</button>
                        <input type="number" class="form-control form-control-sm text-center" value="${item.cantidad}" min="1" 
                               onchange="actualizarCantidad('${item.id}', this.value)">
                        <button class="btn btn-sm btn-outline-secondary" onclick="actualizarCantidad('${item.id}', ${item.cantidad + 1})">+</button>
                    </div>
                    <span class="fw-bold">S/ ${subtotal.toFixed(2)}</span>
                </div>
            </div>
        `;
    });
    
    contenedorCarrito.innerHTML = html;
    if (totalCarrito) totalCarrito.textContent = `S/ ${total.toFixed(2)}`;
}

function vaciarCarrito() {
    carrito = [];
    guardarCarritoEnStorage();
    actualizarContadorCarrito();
    renderizarCarrito();
    mostrarAlerta('info', 'Carrito vaciado');
}

// ============================================
// FORMULARIO DE CONTACTO
// ============================================
function inicializarFormularioContacto() {
    const formContacto = document.getElementById('formContacto');
    
    if (!formContacto) return;
    
    formContacto.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validarFormularioContacto()) {
            this.submit();
        }
    });
}

function validarFormularioContacto() {
    let esValido = true;
    const errores = [];
    
    const nombre = document.getElementById('nombre_contacto');
    const email = document.getElementById('correo_contacto');
    const mensaje = document.getElementById('mensaje_contacto');
    
    if (nombre && nombre.value.trim().length < 3) {
        errores.push('El nombre debe tener al menos 3 caracteres');
        nombre.classList.add('is-invalid');
        esValido = false;
    } else if (nombre) {
        nombre.classList.remove('is-invalid');
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email.value.trim())) {
        errores.push('Ingrese un correo válido');
        email.classList.add('is-invalid');
        esValido = false;
    } else if (email) {
        email.classList.remove('is-invalid');
    }
    
    if (mensaje && mensaje.value.trim().length < 10) {
        errores.push('El mensaje debe tener al menos 10 caracteres');
        mensaje.classList.add('is-invalid');
        esValido = false;
    } else if (mensaje) {
        mensaje.classList.remove('is-invalid');
    }
    
    if (errores.length > 0) {
        mostrarAlerta('danger', errores.join('<br>'));
    }
    
    return esValido;
}

// ============================================
// FUNCIONES DE UTILIDAD
// ============================================
function autoRellenarDatosUsuario() {
    const usuarioData = document.querySelector('[data-usuario-nombre]');
    
    if (usuarioData) {
        const nombre = document.getElementById('nombre_reserva');
        const email = document.getElementById('correo_reserva');
        const telefono = document.getElementById('telefono_reserva');
        
        if (nombre && !nombre.value) nombre.value = usuarioData.dataset.usuarioNombre || '';
        if (email && !email.value) email.value = usuarioData.dataset.usuarioCorreo || '';
        if (telefono && !telefono.value) telefono.value = usuarioData.dataset.usuarioTelefono || '';
    }
}

function mostrarAlerta(tipo, mensaje) {
    let contenedor = document.getElementById('alertasGlobales');
    
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'alertasGlobales';
        contenedor.className = 'position-fixed top-0 end-0 p-3';
        contenedor.style.zIndex = '9999';
        document.body.appendChild(contenedor);
    }
    
    const alertId = 'alerta-' + Date.now();
    const iconos = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const alerta = document.createElement('div');
    alerta.id = alertId;
    alerta.className = `alert alert-${tipo} alert-dismissible fade show shadow`;
    alerta.setAttribute('role', 'alert');
    alerta.innerHTML = `
        <i class="fas fa-${iconos[tipo] || 'info-circle'} me-2"></i>
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    contenedor.appendChild(alerta);
    
    setTimeout(() => {
        const elemento = document.getElementById(alertId);
        if (elemento) {
            elemento.classList.remove('show');
            setTimeout(() => elemento.remove(), 300);
        }
    }, 5000);
}

function mostrarModal(titulo, mensaje, callback) {
    let modal = document.getElementById('modalGenerico');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modalGenerico';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitulo"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalMensaje"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="btnModalConfirmar">
                            <i class="fas fa-check me-2"></i>Confirmar
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    document.getElementById('modalTitulo').textContent = titulo;
    document.getElementById('modalMensaje').innerHTML = mensaje;
    
    const btnConfirmar = document.getElementById('btnModalConfirmar');
    btnConfirmar.onclick = function() {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
        if (callback) callback();
    };
    
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

function autoOcultarAlertas() {
    const alertas = document.querySelectorAll('.alert:not(.alert-dismissible)');
    alertas.forEach(alerta => {
        setTimeout(() => {
            alerta.style.opacity = '0';
            setTimeout(() => alerta.remove(), 300);
        }, 5000);
    });
}


// ============================================
// FUNCIONES GLOBALES EXPUESTAS
// ============================================
window.eliminarDelCarrito = eliminarDelCarrito;
window.actualizarCantidad = actualizarCantidad;
window.vaciarCarrito = vaciarCarrito;
window.renderizarCarrito = renderizarCarrito;
window.mostrarAlerta = mostrarAlerta;
window.mostrarModal = mostrarModal;