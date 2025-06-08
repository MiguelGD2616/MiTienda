@extends('plantilla.app')
@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Nuevo Producto</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('productos.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @include('producto._form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection