@extends('plantilla.app')
@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        <h2 class="mb-3">Listado de Productos</h2>
        @if (isset($categoryCount) && $categoryCount > 0)
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <form action="{{ route('productos.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar producto..." value="{{ $texto }}">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('productos.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nuevo Producto</a>
            </div>
        </div>
        @endif

        @if (session('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Imagen</th> {{-- <-- 1. ENCABEZADO AÑADIDO --}}
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($categoryCount) && $categoryCount > 0)
                    @forelse ($productos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            {{-- 2. CELDA AÑADIDA PARA MOSTRAR LA IMAGEN --}}
                            <td>
                                {{-- Verificamos si el producto tiene una imagen guardada --}}
                                @if ($producto->imagen_url)
                                    {{-- Usamos asset() para generar la URL pública --}}
                                    <img src="{{ cloudinary()->image($producto->imagen_url)->toUrl() }}"
                                        alt="Imagen de {{ $producto->name }}" 
                                        width="60" 
                                        class="img-thumbnail">
                                @else
                                    {{-- Si no hay imagen, mostramos un texto --}}
                                    <span class="text-muted">Sin imagen</span>
                                @endif
                            </td>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->categoria->nombre }}</td>
                            <td>S/.{{ number_format($producto->precio, 2) }}</td>
                            <td>{{ $producto->stock }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                        <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modal-delete-{{ $producto->id }}">
                                            <i class="bi bi-trash-fill text-white"></i>
                                        </button>
                                </div>
                            </td>
                        </tr>
                        @include('producto.delete', ['producto' => $producto])
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No se encontraron productos.</td>
                    </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="8" class="text-center text-muted">No se encontraron productos - Primero debe registrar una categoria.</td>
                    </tr>
                
                @endif
            </tbody>
        </table>
    </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                {{ $productos->links() }}
            </div>
        </div>
    </div>
</main>
@endsection
