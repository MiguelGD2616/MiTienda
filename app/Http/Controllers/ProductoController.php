<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $texto = $request->get('texto');
        
        $productos = Producto::with('categoria')
            ->where('nombre', 'LIKE', '%' . $texto . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $categoryCount = Categoria::count();


        return view('producto.index', compact('productos', 'texto', 'categoryCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categoriaCount = Categoria::count();

        // VALIDACIÓN CLAVE: Si no hay categorías, no podemos crear un producto.
        if ($categoriaCount === 0) {
            // Redirigimos al listado de categorías con un mensaje de advertencia.
            return redirect()->route('categorias.index')
                ->with('warning', '¡Atención! Para crear un producto, primero debe registrar al menos una categoría.');
        }
        
        // Si hay categorías, procedemos normalmente.
        $categorias = Categoria::orderBy('nombre')->get();
        return view('producto.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|imagen|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
        ]);

        $productData = $request->except('imagen_url'); // Obtenemos todo menos el archivo

         if ($request->hasFile('imagen_url')) {
            // CORRECTO: Guardamos el archivo y obtenemos la ruta relativa
            $path = $request->file('imagen_url')->store('products', 'public');
            $productData['imagen_url'] = $path; // Guardamos la ruta (ej: "products/xyz.jpg")
        }

        Producto::create($productData);

        return redirect()->route('productos.index')->with('mensaje', 'Producto creado con éxito.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('producto.edit', compact('producto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
       $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen_url' => 'nullable|imagen|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $productData = $request->except('imagen_url');

        if ($request->hasFile('imagen_url')) {
        // Borramos la imagen antigua
            if ($producto->imagen_url) {
                Storage::disk('public')->delete($producto->imagen_url);
            }
            // Guardamos la nueva y obtenemos la ruta
            $path = $request->file('imagen_url')->store('products', 'public');
            $productData['imagen_url'] = $path;
        }


        $producto->update($productData);

        return redirect()->route('productos.index')->with('mensaje', 'Producto actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        // Borrar la imagen asociada
        if ($producto->imagen_url) {
            Storage::disk('public')->delete($producto->imagen_url);
        }

        $producto->delete();
        return redirect()->route('productos.index')->with('mensaje', 'Producto eliminado con éxito.');
    }

     public function listar(Request $request)
    {
        // Obtenemos todos los productos para la vista pública
        $productos = Producto::with('categoria')
                        ->orderBy('created_at', 'desc')
                        ->paginate(6); // 6 productos por página

        // Devolvemos la vista de la tienda pública
        return view('producto.listar', compact('productos'));
    }
}
