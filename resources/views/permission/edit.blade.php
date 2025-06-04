@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            {{-- Puedes añadir breadcrumbs o títulos aquí si tu plantilla lo requiere --}}
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header"><h3 class="card-title">EDITAR PERMISO: {{ $registro->name }}</h3></div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {{-- Mostrar errores de validación --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{route('permisos.update', $registro->id)}}" method="POST">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 form-group mb-3">
                                        <label for="name" class="form-label">Nombre del Permiso (Slug) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', $registro->name) }}"
                                               placeholder="Ej: modulo-accion (user-list)" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Debe ser único, en minúsculas y usar guiones en lugar de espacios.</small>
                                    </div>

                                    <div class="col-lg-6 form-group mb-3">
                                        <label for="guard_name" class="form-label">Guard Name</label>
                                        <input type="text" class="form-control @error('guard_name') is-invalid @enderror"
                                               id="guard_name" name="guard_name" value="{{ old('guard_name', $registro->guard_name) }}"
                                               placeholder="web">
                                        @error('guard_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Normalmente 'web'. Déjalo así si no estás seguro.</small>
                                    </div>

                                    <div class="col-lg-12 form-group mt-3">
                                        <a href="{{route('permisos.index')}}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Regresar
                                        </a>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-save"></i> Actualizar Permiso
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                        {{-- El card-footer con paginación no tiene sentido en un formulario de edición --}}
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
@endsection