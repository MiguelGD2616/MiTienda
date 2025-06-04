@extends('plantilla.app') {{-- Asumiendo que este es tu layout principal --}}

@section('contenido') {{-- Asegúrate que 'contenido' sea el nombre de sección correcto en tu layout --}}
    <main class="app-main">
        <div class="container-fluid mt-4">
            <!-- Título -->
            <h2 class="mb-3">Listado de Permisos</h2>

            <!-- Fila: Buscar a la izquierda | Nuevo Permiso a la derecha -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-6">
                    <form action="{{ route('permisos.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="texto" placeholder="Buscar permiso..."
                                value="{{ $texto ?? '' }}"> {{-- Usar $texto si viene del controlador --}}
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    @can('permission-create') {{-- Proteger el botón con el permiso --}}
                        <a href="{{ route('permisos.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-lg"></i> Nuevo Permiso
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Mensaje de éxito/error -->
            @if (session('mensaje'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('mensaje') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Tabla -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Nombre del Permiso</th>
                                    <th>Guard Name</th>
                                    <th class="text-center" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registros as $permiso) {{-- $registros es la variable que pasamos desde el controlador --}}
                                    <tr>
                                        <td>{{ $permiso->id }}</td>
                                        <td>{{ $permiso->name }}</td>
                                        <td>{{ $permiso->guard_name }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                @can('permission-edit')
                                                    <a href="{{ route('permisos.edit', $permiso) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                @endcan
                                                @can('permission-delete')
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#modal-delete-permission-{{$permiso->id}}" title="Eliminar">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- No necesitas un @include('permission.delete') a menos que tengas un modal complejo para confirmación de borrado de permisos --}}
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No se encontraron permisos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($registros->hasPages()) {{-- Mostrar footer solo si hay paginación --}}
                    <div class="card-footer text-end">
                        {{ $registros->appends(['texto' => $texto ?? ''])->links() }} {{-- Asegurar que 'texto' se pase a la paginación --}}
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection

@push('js') {{-- O como manejes los scripts en tu plantilla --}}
<script>
    // Confirmación para eliminar
    document.querySelectorAll('.form-permission-delete').forEach(form => {
        form.addEventListener('submit', function(event) {
            // event.preventDefault(); // No es necesario si es un submit directo
            if (!confirm('¿Está seguro de eliminar este permiso? Esta acción no se puede deshacer y podría afectar los roles que lo usan.')) {
                event.preventDefault(); // Prevenir el envío si el usuario cancela
            }
        });
    });
</script>
@endpush