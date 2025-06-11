@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        
        <!-- Título Dinámico -->
        <h2 class="h3 mb-4">
            <i class="fa-solid {{ isset($registro) ? 'fa-user-pen' : 'fa-user-plus' }} me-2"></i>
            {{ isset($registro) ? 'Editar Usuario' : 'Crear Usuario' }}
        </h2>

        <div class="row">
            <!-- Columna del Formulario (70%) -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fa-solid fa-address-card me-2"></i>
                            Información del Usuario
                        </h5>
                    </div>
                    <form action="{{ isset($registro) ? route('usuarios.update', $registro->id) : route('usuarios.store') }}" method="POST" id="formRegistroUsuario">
                        @csrf
                        @if(isset($registro))
                            @method('PUT')
                        @endif
                        
                        <div class="card-body p-4">
                            <p class="text-muted small">
                                Rellena los datos para el nuevo perfil de usuario.
                            </p>
                            <hr class="mb-4">

                            <!-- Nombre y Email -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombre completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $registro->name ?? '') }}" required>
                                    </div>
                                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $registro->email ?? '') }}" required>
                                    </div>
                                    @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>

                            <!-- Contraseñas -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                                    </div>
                                    @if(isset($registro))<small class="form-text text-muted">Dejar en blanco para no cambiar.</small>@endif
                                    @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Rol y Estado -->
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Rol del Usuario</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ (old('role') == $role->name) || (isset($registro) && $registro->hasRole($role->name) && !old('role')) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('role')<small class="text-danger">{{$message}}</small>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label d-block">Estado de la cuenta</label>
                                    <div class="form-check form-switch fs-5">
                                        <input type="hidden" name="activo" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="activoSwitch" name="activo" value="1" {{ old('activo', $registro->activo ?? 1) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activoSwitch" id="activoLabel"></label>
                                    </div>
                                    @error('activo')<small class="text-danger">{{$message}}</small>@enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botones en el Footer -->
                        <div class="card-footer bg-white text-end border-0 pt-0 pb-4 px-4">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary me-2">
                                <i class="fa-solid fa-xmark me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Columna del Panel de Ayuda -->
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
                            <img src="{{ asset('assets/img/usuario.gif') }}" 
                                 class="img-fluid" alt="Gestión de usuarios" style="max-height: 150px;">
                        </div>
                        
                        <h6 class="text-muted"><i class="fa-solid fa-key text-warning me-2"></i>Seguridad y Roles</h6>
                        <p class="text-muted small">
                            Asigna el rol correcto para controlar el acceso del usuario. 
                        </p>
                        
                        <hr>
                        
                        <h6 class="text-muted"><i class="fa-solid fa-tags me-2"></i>Descripción de Roles</h6>
                        <div class="list-group list-group-flush small">
                            @foreach($roles as $role)
                                <div class="list-group-item d-flex justify-content-between align-items-start px-0">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $role->name }}</div>
                                        <span class="text-muted">Acceso a funciones de {{ strtolower($role->name) }}.</span>
                                    </div>
                                </div>
                            @endforeach
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
    /* Estilo base para el switch (estado inactivo, rojo) */
    .form-check-input {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        /* Forzamos la imagen del círculo blanco con !important */
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
    }

    /* Estilo para el switch cuando está activado (verde) */
    .form-check-input:checked {
        background-color: #198754 !important;
        border-color: #198754 !important;
        /* También forzamos la imagen aquí para asegurar consistencia */
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activoSwitch = document.getElementById('activoSwitch');
        const activoLabel = document.getElementById('activoLabel');
        function actualizarEstadoLabel() {
            if (activoSwitch.checked) {
                activoLabel.textContent = 'Activo';
                activoLabel.classList.remove('text-danger');
                activoLabel.classList.add('text-success');
            } else {
                activoLabel.textContent = 'Inactivo';
                activoLabel.classList.remove('text-success');
                activoLabel.classList.add('text-danger');
            }
        }
        actualizarEstadoLabel();
        activoSwitch.addEventListener('change', actualizarEstadoLabel);
    });

    document.getElementById('mnuSeguridad').classList.add('menu-open');
    document.getElementById('itemUsuario').classList.add('active');
</script>
@endpush