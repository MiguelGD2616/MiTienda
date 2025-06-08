{{-- 
Este formulario es un parcial reutilizable para crear y editar productos.
Usa la variable $producto si está definida (en la vista de edición)
o valores antiguos (old) o vacíos si no (en la vista de creación).
--}}

<div class="row">
    <!-- Fila 1: Nombre y Categoría -->
    <div class="col-md-6 mb-3">
        <label for="nombre" class="form-label">Nombre del Producto</label>
        <input 
            type="text" 
            class="form-control @error('nombre') is-invalid @enderror" 
            name="nombre" 
            id="nombre" 
            value="{{ old('nombre', $producto->nombre ?? '') }}" 
            required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="categoria_id" class="form-label">Categoría</label>
        <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
            
            {{-- Comprobamos si la colección de categorías no está vacía --}}
            @if ($categorias->isNotEmpty())
                <option value="">-- Seleccione una categoría --</option>
                @foreach ($categorias as $categoria)
                    <option 
                        value="{{ $categoria->id }}" 
                        {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            @else
                {{-- Mensaje si no hay categorías disponibles --}}
                <option value="">No hay categorías registradas</option>
            @endif

        </select>
        @error('categoria_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        
        {{-- AÑADIMOS UN MENSAJE DE AYUDA --}}
        @if ($categorias->isEmpty())
            <div class="form-text text-danger mt-2">
                No puede crear un producto sin categorías. Por favor, <a href="{{ route('categorias.create') }}">registre una categoría primero</a>.
            </div>
        @endif
    </div>

    <!-- Fila 2: Precio y Stock -->
    <div class="col-md-6 mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input 
            type="number" 
            step="0.01" 
            class="form-control @error('precio') is-invalid @enderror" 
            name="precio" 
            id="precio" 
            value="{{ old('precio', $producto->precio ?? '') }}" 
            required>
        @error('precio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="stock" class="form-label">Stock (Cantidad disponible)</label>
        <input 
            type="number" 
            class="form-control @error('stock') is-invalid @enderror" 
            name="stock" 
            id="stock" 
            value="{{ old('stock', $producto->stock ?? '0') }}" 
            required>
        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Fila 3: Descripción -->
    <div class="col-md-12 mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea 
            name="descripcion" 
            id="descripcion" 
            class="form-control @error('descripcion') is-invalid @enderror" 
            rows="4">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Fila 4: Subida de Imagen -->
    <div class="col-md-12 mb-3">
        <label for="imagen_url" class="form-label">Imagen del Producto</label>
        <input 
            type="file" 
            class="form-control @error('imagen_url') is-invalid @enderror" 
            name="imagen_url" 
            id="imagen_url">
        @error('imagen_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Sección para mostrar la imagen actual (solo en modo edición) -->
    @isset($producto)
        @if ($producto->imagen_url)
            <div class="col-md-12 mb-3">
                <label>Imagen Actual:</label><br>
                <img 
                    src="{{ cloudinary()->image($producto->imagen_url)->toUrl() }}"
                    alt="{{ $producto->nombre }}" 
                    width="150" 
                    class="img-thumbnail">
            </div>
        @endif
    @endisset
</div>

<hr>

<!-- Botones de Acción -->
<div class="text-end">
    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button class="btn btn-primary" type="submit">Guardar Producto</button>
</div>