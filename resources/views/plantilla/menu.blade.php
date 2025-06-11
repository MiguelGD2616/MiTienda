@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // Colores y logo por defecto (admin)
    $colorFondo = '#6076e5';
    $colorTexto = 'text-white';
    $logoPath = asset('assets/img/MiTienda.png');

    // Si el rol es Cafeteria, usamos el nuevo azul
    if ($user && $user->hasRole('Cafeteria')) {
        $colorFondo = '#582304';
        $colorTexto = 'text-light';
        $logoPath = asset('assets/img/coffe.png');
    }

    // Verifica si estás en una de las rutas del submenú Seguridad
    $seguridadActiva = request()->routeIs('usuarios.index') ||
        request()->routeIs('roles.index') ||
        request()->routeIs('categorias.index') ||
        request()->routeIs('permisos.index') ||
        request()->routeIs('mostrarProductosPublico') ||
        request()->routeIs('productos.index');
@endphp

<aside class="app-sidebar shadow d-flex flex-column {{ $colorTexto }}" style="min-height: 100vh; background-color: {{ $colorFondo }};">
    <div class="d-flex justify-content-center py-4" style="background-color: {{ $colorFondo }};">
        <img src="{{ $logoPath }}" alt="Logo" style="height: 150px;">
    </div>
    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper flex-grow-1 px-2 pt-2">
        <nav class="nav flex-column">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ $colorTexto }}">
                    <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
                </a>
            </div>

            <!-- Seguridad -->
            @canany(['user-list', 'rol-list', 'categoria-list', 'producto-list'])
                <div class="nav-item mt-3">
                    <a class="nav-link {{ $colorTexto }} d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" href="#seguridadMenu" role="button"
                        aria-expanded="{{ $seguridadActiva ? 'true' : 'false' }}" aria-controls="seguridadMenu">
                        <span><i class="fa-solid fa-shield-halved me-2"></i> Seguridad</span>
                        <i class="fa-solid {{ $seguridadActiva ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $seguridadActiva ? 'show' : '' }}" id="seguridadMenu">
                        @can('user-list')
                            <a href="{{ route('usuarios.index') }}"
                                class="nav-link {{ request()->routeIs('usuarios.index') ? 'active-item' : $colorTexto }}">
                                <i class="fa-solid fa-users me-2"></i> Usuarios
                            </a>
                        @endcan

                        @can('rol-list')
                            <a href="{{ route('roles.index') }}"
                                class="nav-link {{ request()->routeIs('roles.index') ? 'active-item' : $colorTexto }}">
                                <i class="fa-solid fa-user-tag me-2"></i> Roles
                            </a>
                        @endcan

                        @can('categoria-list')
                            <a href="{{ route('categorias.index') }}"
                                class="nav-link {{ request()->routeIs('categorias.index') ? 'active-item' : $colorTexto }}">
                                <i class="fa-solid fa-tags me-2"></i> Categorías
                            </a>
                        @endcan

                        @can('permission-list')
                            <a href="{{ route('permisos.index') }}"
                                class="nav-link {{ request()->routeIs('permisos.index') ? 'active-item' : $colorTexto }}">
                                <i class="fa-solid fa-key me-2"></i> Permisos
                            </a>
                        @endcan

                        @can('producto-list')
                            <a href="{{ route('productos.index') }}"
                                class="nav-link {{ request()->routeIs('productos.index') ? 'active-item' : $colorTexto }}">
                                <i class="fa-solid fa-box-open me-2"></i> Productos
                            </a>
                        @endcan

                        @can('tienda')
                            <a href="{{ route('mostrarProductosPublico', auth()->user()) }}"
                                class="nav-link {{ request()->routeIs('mostrarProductosPublico') ? 'active-item' : $colorTexto }}">
                                <i class="fa-solid fa-store me-2"></i> Ver Tienda
                            </a>

                            <a href="#" class="nav-link {{ request()->routeIs('#') ? 'active-item' : $colorTexto }}" data-bs-toggle="modal" data-bs-target="#shareLinkModal">
                                <i class="fa-solid fa-share-nodes me-2"></i> Link de Tienda
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany
        </nav>
    </div>
</aside>