@extends('welcome.app')

@section('title', isset($categoriaActual) ? 'Tienda: ' . $categoriaActual->nombre : 'Tienda de ' . $tienda_user->name) {{-- // <-- MEJORA: Título más personalizado --}}


@section('contenido')
<div class="container-contenido">
    
    <div class="text-center mb-5">
        @if (isset($categoriaActual))
            <h1 style="font-family: 'Poppins', sans-serif; font-weight: 900;">Categoría: {{ $categoriaActual->nombre }}</h1>
            <p class="text-muted">Mostrando productos de la tienda de {{ $tienda_user->name }}</p>
        @else
            <h1 style="font-family: 'Poppins', sans-serif; font-weight: 900;">Tienda de {{ $tienda_user->name }}</h1>
            <p class="text-muted">Explora todos nuestros productos.</p>
        @endif
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6">
            <label for="buscador-categoria" class="form-label"><b>Busca una Categoría para filtrar los productos:</b></label>
            <div class="position-relative">
                <input type="text" 
                    id="buscador-categoria" 
                    class="form-control" 
                    placeholder="Escribe el nombre de una categoría..."
                    autocomplete="off"
                    {{-- // <-- CORRECCIÓN 1: Pasar la URL de búsqueda dinámica al input usando un atributo data-* --}}
                    data-search-url="{{ route('tienda.buscar', $tienda_user) }}">
                
                <div id="resultados-categorias" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
            </div>
            
            <div class="mt-2">
                {{-- Este enlace ya estaba bien --}}
                <a href="{{ route('mostrarProductosPublico', $tienda_user) }}" class="btn btn-sm btn-outline-secondary">Ver Todos los Productos</a>
                @if(isset($categoriaActual))
                    <span class="ms-2">Mostrando productos de: <b>{{ $categoriaActual->nombre }}</b></span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="card-container">
        @forelse ($productos as $producto)
            <div class="custom-card">
                <div class="img-box">
                    <img src="{{ $producto->imagen_url ? cloudinary()->image($producto->imagen_url)->toUrl() : 'https://via.placeholder.com/300x220.png?text=Producto' }}" 
                         alt="Imagen de {{ $producto->nombre }}">
                </div>
                <div class="custom-content">
                    <h2>{{ $producto->nombre }}</h2>
                    <div class="price">${{ number_format($producto->precio, 2) }}</div>
                    <a href="#">Ver Detalles</a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                @if (isset($categoriaActual))
                    <h3 class="text-muted">No hay productos en esta categoría.</h3>
                @else
                    <h3 class="text-muted">No hay productos para mostrar en este momento.</h3>
                @endif
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $productos->links() }}
    </div>

</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Definimos la URL base para los enlaces de los resultados aquí
    // Esto es más limpio y fácil de mantener.
    // La ruta 'productos.categoria' necesita un placeholder para el ID de la categoría que reemplazaremos luego.
    // Usamos un placeholder único como '__ID__'
    const categoriaUrlTemplate = "{{ route('productos.categoria', ['tienda_user' => $tienda_user, 'categoria' => '__ID__']) }}";

    $('#buscador-categoria').on('keyup', function() {
        let query = $(this).val();
        let resultadosDiv = $('#resultados-categorias');
        
        // <-- CORRECCIÓN 2: Obtener la URL de búsqueda desde el atributo data-* del input
        let searchUrl = $(this).data('search-url');

        if (query.length < 2) {
            resultadosDiv.html('');
            return;
        }

        $.ajax({
            url: searchUrl, // <-- CORRECCIÓN 3: Usar la URL dinámica
            type: "GET",
            data: { 'q': query },
            success: function(data) {
                resultadosDiv.html('');

                if (data.length === 0) {
                    resultadosDiv.append('<a href="#" class="list-group-item list-group-item-action disabled">No se encontraron categorías</a>');
                    return;
                }

                $.each(data, function(index, categoria) {
                    // <-- CORRECCIÓN 4: Construir la URL del enlace reemplazando el placeholder
                    let url = categoriaUrlTemplate.replace('__ID__', categoria.id);
                    resultadosDiv.append('<a href="' + url + '" class="list-group-item list-group-item-action">' + categoria.nombre + '</a>');
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Es buena práctica manejar errores
                console.error("Error en la búsqueda:", textStatus, errorThrown);
                resultadosDiv.html('<a href="#" class="list-group-item list-group-item-action list-group-item-danger">Error al buscar</a>');
            }
        });
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscador-categoria, #resultados-categorias').length) {
            $('#resultados-categorias').html('');
        }
    });
});
</script>

@endpush
@endsection