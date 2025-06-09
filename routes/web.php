<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PerfilController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PermissionController; 

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/soporte', function () {
    return view('soporte');
})->name('soporte');



Route::get('/categorias/lista', [CategoriaController::class, 'listar'])->name('categorias.list');
Route::get('/{tienda_user:id}/tienda', [ProductoController::class, 'mostrarProductosPublico'])->name('mostrarProductosPublico');

//Route::get('/tienda', [ProductoController::class, 'mostrarProductosPublico'])->name('mostrarProductosPublico');
Route::get('/{tienda_user}/tienda/buscar', [ProductoController::class, 'buscarPublico'])->name('tienda.buscar');
//Route::get('/api/buscar-categorias', [App\Http\Controllers\CategoriaController::class, 'buscarPublico'])->name('api.categorias.buscar');
// Ruta para ver productos filtrados por categoría
//Route::get('/tienda/categoria/{categoria}', [ProductoController::class, 'filtrarPorCategoriaPublico'])->name('productos.categoria');
Route::get('/{tienda_user:id}/tienda/categoria/{categoria}', [ProductoController::class, 'filtrarPorCategoriaPublico'])->name('productos.categoria');
Route::middleware(['auth'])->group(function(){
    Route::resource('usuarios', UserController::class);
    Route::patch('usuarios/{usuario}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
    Route::resource('roles', RoleController::class);

    Route::get('dashboard', function(){
        return view('dashboard');
    })->name('dashboard');

    Route::post('logout', function(){
        Auth::logout();
        return redirect('/login');
    })->name('logout');

    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::resource('permisos', PermissionController::class)->except(['show']);
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('productos', ProductoController::class)->except(['show']);
});

Route::middleware('guest')->group(function(){
    Route::get('login', function(){
        return view('autenticacion.login');
    })->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/registro', [RegisterController::class, 'showRegistroForm'])->name('registro');
    Route::post('/registro', [RegisterController::class, 'registrar'])->name('registro.store');

    Route::get('password/reset', [ResetPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.send-link');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
    
});