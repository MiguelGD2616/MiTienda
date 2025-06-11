<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sistema</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free Website Template" name="keywords">
    <meta content="Free Website Template" name="description">

    <!-- Favicon -->
    <link href="{{asset('assets/img/ventas.png')}}" rel="icon">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    @stack('estilos')
    @vite(['resources/css/welcome.css'])
    @stack('styles')

    @push('scripts')
    <script>
        // Nos aseguramos de que el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function () {
            const copyButton = document.getElementById('copyLinkBtn');
            const linkInput = document.getElementById('storeLinkInput');

            // Solo añadimos el evento si el botón existe en la página
            if (copyButton) {
                copyButton.addEventListener('click', function () {
                    const linkToCopy = linkInput.value;

                    // Usamos la API moderna del Portapapeles (es asíncrona)
                    navigator.clipboard.writeText(linkToCopy).then(function() {
                        // Éxito: Damos feedback al usuario
                        const originalText = copyButton.innerHTML;
                        copyButton.innerHTML = '<i class="bi bi-check-lg me-1"></i> ¡Copiado!';
                        
                        // Volvemos al texto original después de 2 segundos
                        setTimeout(function() {
                            copyButton.innerHTML = originalText;
                        }, 2000);

                    }).catch(function(err) {
                        // Error: Por si algo falla (muy raro en navegadores modernos)
                        console.error('Error al intentar copiar el enlace: ', err);
                        alert('Error al copiar. Por favor, selecciona el texto manualmente.');
                    });
                });
            }
        });
    </script>
    @endpush
    
    {{-- Modal para Compartir Enlace de la Tienda --}}
    @auth {{-- Solo lo creamos si el usuario está logueado --}}
    <div class="modal fade" id="shareLinkModal" tabindex="-1" aria-labelledby="shareLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="shareLinkModalLabel">
            <i class="bi bi-share-fill me-2"></i> ¡Comparte tu Tienda!
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p>Este es el enlace público a tu tienda. Cópialo y compártelo con tus clientes en redes sociales, WhatsApp, etc.</p>
            
            <div class="input-group">
                {{-- Usamos un input de solo lectura para mostrar el enlace --}}
                <input type="text" class="form-control" 
                    value="{{ route('mostrarProductosPublico', auth()->user()) }}" 
                    id="storeLinkInput" readonly>
                
                {{-- El botón que copiará el texto --}}
                <button class="btn btn-primary" type="button" id="copyLinkBtn">
                    <i class="bi bi-clipboard me-1"></i> Copiar
                </button>
            </div>

        </div>
        </div>
    </div>
    </div>
    @endauth
</head>

<body>
    <div class="app-wrapper">
        <!-- Navbar Start -->
        <div class="container-fluid p-0 nav-bar">
            @include('welcome.navbar')
        </div>
        <!-- Navbar End -->
        <main class="app-main">
            @yield('contenido')
        </main>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    @stack('scripts')
</body>

</html>