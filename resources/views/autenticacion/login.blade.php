@extends('autenticacion.app')
@section('titulo', 'Sistema - Login')

@section('contenido')
  {{-- FontAwesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  {{-- Estilos personalizados con Vite --}}
  @vite(['resources/css/login.css'])

  <!-- 🎥 Video de fondo solo en pantallas grandes -->
  <video autoplay loop muted playsinline controlslist="nodownload" class="video-background d-none d-lg-block">
    <source src="{{ asset('assets/video/fondo.mp4') }}" type="video/mp4">
    Tu navegador no soporta el video.
  </video>

  <!-- 💡 Contenedor principal de la página -->
  <div class="login-page-container">
      {{-- Este div ocupará el espacio de la parte "naranja" del video en pantallas grandes --}}
      <div class="left-video-spacer d-none d-lg-block">
          {{-- Contenido vacío, solo ocupa espacio --}}
      </div>

      {{-- Esta columna contendrá el formulario y se alineará a la derecha del spacer --}}
      <div class="login-form-column">
          <div class="login-content p-4 shadow-lg rounded">
              <form action="{{ route('login.post') }}" method="POST">
                  @csrf

                  <div class="text-center mb-4">
                      <img src="{{ asset('assets/img/login.png') }}" alt="Login" style="width: 80px;">
                      <h2 class="title mt-3">BIENVENIDO</h2>
                  </div>

                  {{-- Mensajes de error --}}
                  @if(session('error'))
                  <div class="alert alert-danger text-start">
                      {{ session('error') }}
                  </div>
                  @endif

                  @if(Session::has('mensaje'))
                  <div class="alert alert-info alert-dismissible fade show mt-2">
                      {{ Session::get('mensaje') }}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                  </div>
                  @endif

                  {{-- Validaciones --}}
                  @if ($errors->any())
                  <div class="alert alert-danger text-start">
                      <ul class="mb-0 ps-3">
                          @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                  @endif

                  <div class="input-div one mb-3">
                      <div class="i"><i class="fas fa-user text-danger"></i></div>
                      <div class="div">
                          <input type="email" name="email" value="{{ old('email') }}" class="input" placeholder="Ingresa Email">
                      </div>
                  </div>

                  <div class="input-div pass mb-3">
                      <div class="i"><i class="fas fa-lock text-danger"></i></div>
                      <div class="div">
                          <input type="password" name="password" class="input" placeholder="Ingresa Password">
                      </div>
                  </div>

                  <a href="{{ route('password.request') }}" class="d-block text-end mb-3">¿Olvidaste tu contraseña?</a>
                  <input type="submit" class="btn btn-danger w-100" value="ACCEDER">
              </form>
          </div>
      </div>
  </div>
@endsection