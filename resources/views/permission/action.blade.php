@extends('plantilla.app') {{-- O tu layout principal --}}

@section('title', $registro->id ? 'Editar Permiso' : 'Crear Permiso')

@section('content_header')
    <h1>{{ $registro->id ? 'Editar Permiso: ' . $registro->name : 'Crear Nuevo Permiso' }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title">{{ $registro->id ? 'Formulario de Edición' : 'Formulario de Creación' }}</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> ¡Ups! Algo salió mal.</h5>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ $registro->id ? route('permisos.update', $registro) : route('permisos.store') }}">
                        @csrf
                        @if ($registro->id)
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Nombre del Permiso (Slug) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $registro->name) }}" required
                                   placeholder="Ej: categoria-list, producto-crear">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Debe ser único y en formato de slug (minúsculas, guiones en lugar de espacios).
                                Por ejemplo: `nombre-recurso-accion`.
                            </small>
                        </div>

                        {{-- Opcional: Campo para Guard Name, generalmente no se necesita cambiar para 'web' --}}
                        {{--
                        <div class="form-group">
                            <label for="guard_name">Guard Name</label>
                            <input type="text" class="form-control @error('guard_name') is-invalid @enderror"
                                   id="guard_name" name="guard_name" value="{{ old('guard_name', $registro->guard_name ?? 'web') }}"
                                   placeholder="web">
                            @error('guard_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Normalmente 'web'. Déjalo en blanco o 'web' para aplicaciones web estándar.
                            </small>
                        </div>
                        --}}

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ $registro->id ? 'Actualizar Permiso' : 'Crear Permiso' }}
                            </button>
                            <a href="{{ route('permisos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection