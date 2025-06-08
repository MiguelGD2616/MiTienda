@extends('autenticacion.app')
@section('titulo', 'Sistema - Registro')

@section('contenido')
<div class="container my-4 mx-auto" style="max-width: 900px;">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0 text-center text-md-start">Nuevo Registro de Usuario</h4>
    </div>
    <div class="card-body">
      {{-- AQUI COMIENZA LA ESTRUCTURA DE 2 COLUMNAS --}}
      <div class="row g-4 align-items-center mt-3">

        <!-- Columna de la imagen (del diseño que te gustó) -->
        <div class="col-12 col-md-4 text-center">
          <img src="{{ asset('assets/img/usuario.gif') }}" alt="Icono de registro" class="img-fluid">
        </div>

        <!-- Columna del formulario (con TUS campos de registro) -->
        <div class="col-12 col-md-8">
          {{-- TU FORMULARIO ORIGINAL VA AQUÍ DENTRO --}}
          <form action="{{ route('registro.store') }}" method="POST">
            @csrf

            {{-- Mensaje de error de sesión (si existe) --}}
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Nombre (Tu campo original) --}}
            <div class="mb-3">
              <label for="name" class="form-label">Nombre completo</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror" placeholder="Ej. Juan Pérez">
              </div>
              @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Email (Tu campo original) --}}
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@correo.com">
              </div>
              @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Contraseña (Tu campo original) --}}
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 6 caracteres">
              </div>
              @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Confirmar contraseña (Tu campo original) --}}
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Repita la contraseña">
              </div>
              @error('password_confirmation')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Rol (Tu campo original) --}}
            <div class="mb-4">
              <label for="categoria" class="form-label">Rol de usuario</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                <select id="categoria" name="categoria"
                        class="form-select @error('categoria') is-invalid @enderror" required>
                  <option value="" disabled selected>Seleccione un rol</option>
                  @foreach ($roles as $rol)
                    @if (!in_array($rol->name, ['admin', 'cliente']))
                      <option value="{{ $rol->name }}" {{ old('categoria') == $rol->name ? 'selected' : '' }}>
                        {{ ucfirst($rol->name) }}
                      </option>
                    @endif
                  @endforeach
                </select>
              </div>
              @error('categoria')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Botón (Tu botón original) --}}
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus me-1"></i> Registrarse
              </button>
            </div>
          </form>
          {{-- FIN DE TU FORMULARIO ORIGINAL --}}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection