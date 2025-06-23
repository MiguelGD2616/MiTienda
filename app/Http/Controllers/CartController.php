<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Muestra la página del carrito
    public function index(Empresa $empresa = null)
    {
        $cartItems = session('cart', []);
        $cartTotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        
        // Pasamos la empresa (si existe) a la vista.
        return view('tienda.cart', compact('cartItems', 'cartTotal', 'empresa'));
    }

    /**
     * Añade un producto al carrito.
     * CORREGIDO para generar la URL de Cloudinary de forma robusta.
     */
    public function add(Request $request, Producto $producto)
    {
        $producto->load('empresa');
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        $imageUrl = null;
        if ($producto->imagen_url) {
            try {
                // Usamos el helper global cloudinary() que es más directo.
                // Le pasamos el public_id completo que guardaste en la base de datos.
                $imageUrl = cloudinary()->image($producto->imagen_url)->toUrl();
            } catch (\Exception $e) {
                // Si por alguna razón el public_id es inválido, evitamos un error fatal.
                \Log::error("Error al generar URL de Cloudinary para el producto {$producto->id}: " . $e->getMessage());
                $imageUrl = null; // La imagen no se mostrará en el carrito, pero la app no se romperá.
            }
        }

        if(isset($cart[$producto->id])) {
            $cart[$producto->id]['quantity'] += $quantity;
        } else {
            $cart[$producto->id] = [
                "id" => $producto->id,
                "name" => $producto->nombre,
                "quantity" => $quantity,
                "price" => $producto->precio,
                "image" => $imageUrl, // Usamos la URL generada
                "tienda_slug" => $producto->empresa->slug,
                "tienda_nombre" => $producto->empresa->nombre,
            ];
        }

        session()->put('cart', $cart);
        return back()->with('mensaje', '¡"'.$producto->nombre.'" añadido al carrito!');
    }

    // El resto de los métodos (update, remove, clear, checkout) no necesitan cambios,
    // ya que no interactúan directamente con la URL de la imagen.

    public function update(Request $request)
    {
        $cart = session()->get('cart');
        if(isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
        return back()->with('mensaje', 'Carrito actualizado.');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart');
        if(isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }
        return back()->with('mensaje', 'Producto eliminado del carrito.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('mensaje', 'El carrito ha sido vaciado.');
    }

    public function checkout(Request $request)
    {
        $cartItems = session('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('welcome')->with('error', 'Tu carrito está vacío.');
        }

        $pedido = DB::transaction(function () use ($cartItems, $request) {
            $user = Auth::user();
            $cliente = Cliente::where('user_id', $user->id)->firstOrFail();
            
            
            $firstItem = reset($cartItems);
            $empresa_id = Producto::find($firstItem['id'])->empresa_id;
            $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

            $nuevoPedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'empresa_id' => $empresa_id,
                'total' => $total,
                'estado' => 'pendiente',
                'notas' => $request->input('notas')
            ]);

            foreach ($cartItems as $item) {
                $nuevoPedido->detalles()->create([
                    'producto_id' => $item['id'],
                    'cantidad' => $item['quantity'],
                    'precio_unitario' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }
            return $nuevoPedido;
        });

        session()->forget('cart');
        return redirect()->route('pedido.success', $pedido);
    }
}