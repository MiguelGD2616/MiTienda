<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PerfilController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Middleware\RedirectAdminsFromWelcome;
use App\Http\Middleware\RememberStoreUrl;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PedidoController;
use App\Models\Pedido;


Route::get('/', function () {
    return view('welcome');
})->middleware([RedirectAdminsFromWelcome::class])->name('welcome');

Route::get('/soporte', function () {
    return view('soporte');
})->middleware([RedirectAdminsFromWelcome::class])->name('soporte');

// ----------------------------
// 🔓 Tienda Pública
// ----------------------------
Route::get('/categorias/lista', [CategoriaController::class, 'listar'])->middleware([RedirectAdminsFromWelcome::class])->name('categorias.list');
// Rutas de la tienda pública
Route::middleware('auth')->post('/carrito/agregar/{producto}', [CartController::class, 'add'])->name('cart.add');   
Route::middleware([RememberStoreUrl::class])->group(function () {
    Route::get('/tienda/{empresa:slug}', [ProductoController::class, 'mostrarTienda'])->name('tienda.public.index');
    Route::get('/tienda/{empresa:slug}/categoria/{categoria}', [ProductoController::class, 'filtrarPorCategoria'])->name('tienda.public.categoria');
});// Ruta para la búsqueda AJAX
Route::get('/tienda/{empresa:slug}/buscar-categorias', [CategoriaController::class, 'buscarPublico'])->name('tienda.categorias.buscar');// ----------------------------
Route::get('/tienda/{empresa:slug}/buscar-productos', [App\Http\Controllers\ProductoController::class, 'buscarPublicoAjax'])->name('tienda.productos.buscar_ajax');
// 🔒 Acceso autenticado
// ----------------------------
Route::middleware(['auth'])->group(function () {
    
    // Dashboard general
    Route::get('dashboard', function(){
        return view('dashboard');
    })->middleware([RememberStoreUrl::class])->name('dashboard');

    // Logout
    Route::post('logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');

    // Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::get('/perfil_cliente', [PerfilController::class, 'edit'])->name('perfil.edit2');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // Gestión de usuarios y roles
    Route::resource('usuarios', UserController::class);
    Route::patch('usuarios/{usuario}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
    
    Route::resource('roles', RoleController::class);
    Route::resource('permisos', PermissionController::class)->except(['show']);

    // Categorías y Productos
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('productos', ProductoController::class)->except(['show']);

    // NUEVO: Gestión de empresas (solo super admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('empresas', EmpresaController::class);
    });

    // NUEVO: Gestión de clientes (usuarios con rol cliente)
    Route::middleware('role:admin|super_admin')->group(function () {
        Route::resource('clientes', ClienteController::class)->only(['index', 'show', 'destroy']);
    });
});

// ----------------------------
// 🧑 Invitados (login / registro / recuperación)
// ----------------------------
Route::middleware('guest')->group(function () {
    Route::get('login', fn() => view('autenticacion.login'))->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/registro', [RegisterController::class, 'showRegistroForm'])->name('registro');
    Route::post('/registro', [RegisterController::class, 'registrar'])->name('registro.store');

    Route::get('password/reset', [ResetPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.send-link');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    // Carrito de Compras
    Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
    Route::post('/carrito/agregar/{producto}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/carrito/actualizar', [CartController::class, 'update'])->name('cart.update');
    Route::post('/carrito/remover', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/carrito/limpiar', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/pedido/procesar', [CartController::class, 'checkout'])->name('cart.checkout');

    // Éxito del Pedido
    Route::get('/pedido-exitoso/{pedido}', function(Pedido $pedido) {
        if (auth()->id() !== $pedido->cliente->user_id) abort(403);
        
        $pedido->load('detalles.producto', 'empresa', 'cliente');

        // --- CONSTRUIMOS UNA ÚNICA VERSIÓN DEL TEXTO ---
        $resumenWeb = "*¡Nuevo Pedido!* 🛍️\n\n" .
                    "*Referencia:* #" . $pedido->id . "\n" .
                    "*Cliente:* " . $pedido->cliente->nombre . "\n" .
                    "*Fecha:* " . $pedido->created_at->format('d/m/Y') . "\n" .
                    "-----------------------------------\n" .
                    "*DETALLE:*\n";
        
        foreach($pedido->detalles as $detalle) {
            $resumenWeb .= "- {$detalle->cantidad}x {$detalle->producto->nombre} = S/." . number_format($detalle->subtotal, 2) . "\n";
        }

        $resumenWeb .= "-----------------------------------\n" .
                    "*TOTAL: S/." . number_format($pedido->total, 2) . "*";

        if($pedido->notas) {
            $resumenWeb .= "\n\n*Notas:* " . $pedido->notas;
        }
        
        // Pasamos solo 'pedido' y 'resumenWeb'
        return view('tienda.success', compact('pedido', 'resumenWeb'));

    })->name('pedido.success');
    
    Route::resource('pedidos', PedidoController::class)->only(['index', 'show']);

    Route::post('/pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelarPorCliente'])->name('pedidos.cliente.cancelar');
    Route::post('/pedidos/{pedido}/actualizar-estado', [PedidoController::class, 'update'])->name('pedidos.updateStatus');
    Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy'); // Si usas el método DELETE
});