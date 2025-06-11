@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        <!-- Título Principal -->
        <h2 class="h3 mb-4">
            <i class="fa-solid fa-tags me-2"></i>
            Gestión de Categorías
        </h2>

        <div class="row">
            <!-- Columna del Formulario (70%) -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fa-solid fa-pen-to-square me-2"></i>
                            Editar Categoría
                        </h5>
                    </div>
                    {{-- La acción del formulario apunta a la ruta de actualización --}}
                    <form action="{{ route('categorias.update', $registros->id) }}" method="post" autocomplete="off">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body p-4">
                            <p class="text-muted small">
                                Actualiza los datos de la categoría.
                            </p>
                            <hr class="mb-4">

                            <!-- CAMPO NOMBRE (con los datos del registro) -->
                            <div class="form-floating mb-3">
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       placeholder="Nombre de la Categoría" 
                                       value="{{ old('nombre', $registros->nombre) }}" required>
                                <label for="nombre">Nombre de la Categoría <span class="text-danger">*</span></label>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- CAMPO DESCRIPCIÓN (con los datos del registro) -->
                            <div class="form-floating mb-4">
                                <input type="text"
                                       class="form-control @error('descripcion') is-invalid @enderror"
                                       id="descripcion"
                                       name="descripcion"
                                       placeholder="Descripción (Opcional)"
                                       value="{{ old('descripcion', $registros->descripcion) }}">
                                <label for="descripcion">Descripción (Opcional)</label>
                                 @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white text-end border-0 pt-0 pb-4 px-4">
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary me-2">
                                <i class="fa-solid fa-xmark me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar Cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Columna Lateral del Panel de Ayuda -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Panel de Ayuda
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/img/empresa.gif') }}" 
                                 class="img-fluid" 
                                 alt="Ilustración de organización" 
                                 style="max-height: 150px;">
                        </div>
                        
                        <h6 class="text-muted"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Consejos Clave</h6>
                        <ul class="list-unstyled text-muted small ps-3">
                            <li class="mb-2">
                                <i class="fa-solid fa-check-double text-success me-1"></i>
                                <strong>Nombres Cortos y Claros:</strong> Usa nombres que describan el grupo de productos.
                            </li>
                            <li>
                                <i class="fa-solid fa-check-double text-success me-1"></i>
                                <strong>Sé Consistente:</strong> Mantén un estilo similar en todas tus categorías.
                            </li>
                        </ul>
                        
                        <hr>
                        
                        <h6 class="text-muted"><i class="fa-solid fa-star me-2"></i>Buenos Ejemplos</h6>
                        <div class="list-group list-group-flush small">
                            <div class="list-group-item px-0"><i class="fa-solid fa-mug-hot text-brown me-2"></i> Bebidas Calientes</div>
                            <div class="list-group-item px-0"><i class="fa-solid fa-ice-cream text-info me-2"></i> Postres y Dulces</div>
                            <div class="list-group-item px-0"><i class="fa-solid fa-burger text-warning me-2"></i> Snacks Salados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('estilos')
<style>
    .text-brown { color: #8B4513; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Asumiendo que el menú de seguridad contiene a las categorías
        const menuSeguridad = document.getElementById('mnuSeguridad');
        if (menuSeguridad) {
            menuSeguridad.classList.add('menu-open');
        }
        
        // Asumiendo que el item del menú de categorías tiene el id 'itemCategoria'
        const itemCategoria = document.getElementById('itemCategoria');
        if (itemCategoria) {
            itemCategoria.classList.add('active');
        }
    });
</script>
@endpush