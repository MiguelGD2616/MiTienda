@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // Colores y logo por defecto
    $colorFondo = '#00406d'; // Azul por defecto (Admin)
    $colorTexto = 'text-white';
    $logoPath = asset('assets/img/MiTienda.png');

    // Personalización por rol
    if ($user && $user->hasRole('Cafeteria')) {
        $colorFondo = '#582304'; // Café
        $colorTexto = 'text-light';
        $logoPath = asset('assets/img/coffe.png');
    }

    // Lógica para mantener el menú de Seguridad abierto
    $seguridadActiva = request()->routeIs([
        'usuarios.index',
        'roles.index',
        'categorias.index',
        'permisos.index',
        'productos.index',
        'tienda.public.index'
    ]);
@endphp

<aside class="app-sidebar shadow d-flex flex-column {{ $colorTexto }}" style="min-height: 100vh; background-color: {{ $colorFondo }};">
    <div class="d-flex justify-content-center py-4" style="background-color: {{ $colorFondo }};">
        <img src="{{ $logoPath }}" alt="Logo" style="height: 150px;">
    </div>

    <div class="sidebar-wrapper flex-grow-1 px-2 pt-2">
        <nav class="nav flex-column">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ $colorTexto }}">
                    <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
                </a>
            </div>

            <!-- Menú de Seguridad (para Admins) -->
            @canany(['user-list', 'rol-list', 'categoria-list', 'permission-list'])
                <div class="nav-item mt-3">
                    <a class="nav-link {{ $colorTexto }} d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" href="#seguridadMenu" role="button"
                        aria-expanded="{{ $seguridadActiva ? 'true' : 'false' }}" aria-controls="seguridadMenu">
                        <span><i class="fa-solid fa-shield-halved me-2"></i> Administración</span>
                        <i class="fa-solid {{ $seguridadActiva ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $seguridadActiva ? 'show' : '' }}" id="seguridadMenu">
                        @can('user-list')
                            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-users me-2"></i> Usuarios</a>
                        @endcan
                        @can('rol-list')
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-user-tag me-2"></i> Roles</a>
                        @endcan
                        @can('permission-list')
                            <a href="{{ route('permisos.index') }}" class="nav-link {{ request()->routeIs('permisos.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-key me-2"></i> Permisos</a>
                        @endcan
                    </div>
                </div>
            @endcanany
            
            <!-- Menú de Gestión de Tienda (para Vendedores/Cafetería) -->
            @canany(['producto-list', 'categoria-list'])
                <div class="nav-item mt-3">
                    <a class="nav-link {{ $colorTexto }} d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" href="#tiendaMenu" role="button"
                        aria-expanded="{{ $seguridadActiva ? 'true' : 'false' }}" aria-controls="tiendaMenu">
                        <span><i class="fa-solid fa-store me-2"></i> Mi Tienda</span>
                        <i class="fa-solid {{ $seguridadActiva ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $seguridadActiva ? 'show' : '' }}" id="tiendaMenu">
                        @can('categoria-list')
                             <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-tags me-2"></i> Categorías</a>
                        @endcan
                        
                        @can('producto-list')
                            <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-box-open me-2"></i> Productos</a>
                        @endcan

                        @can('pedido-list')
                             <a href="{{ route('pedidos.index') }}" class="nav-link {{ request()->routeIs('pedidos.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Pedidos</a>
                        @endcan

                        @if(auth()->user()->hasRole('super_admin'))
                            <a href="#" class="nav-link {{ $colorTexto }}" data-bs-toggle="modal" data-bs-target="#selectEmpresaModal">
                                <i class="fa-solid fa-share-nodes me-2"></i> Compartir / Ver Tienda
                            </a>
                        @else
                            <a href="#" class="nav-link {{ $colorTexto }}" data-bs-toggle="modal" data-bs-target="#shareLinkModal">
                                <i class="fa-solid fa-share-nodes me-2"></i> Compartir Tienda
                            </a>

                            <a href="{{ route('tienda.public.index', auth()->user()->empresa) }}" target="_blank" class="nav-link {{ $colorTexto }}">
                                <i class="fa-solid fa-eye me-2"></i> Ver Tienda
                            </a>
                        @endif
                    </div>
                </div>
            @endcanany

        </nav>
    </div>
</aside>