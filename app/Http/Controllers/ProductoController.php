<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $texto = $request->get('texto'); // Solo una vez
    $userId = auth()->id();

    // Filtramos productos que pertenezcan a categorías del usuario autenticado
    $productos = Producto::with('categoria')
        ->whereHas('categoria', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('nombre', 'LIKE', '%' . $texto . '%')
        ->orderBy('id', 'desc')
        ->paginate(10);
    
    // Contamos categorías y las obtenemos para la vista
    $categoryCount = Categoria::where('user_id', $userId)->count();
    $categorias = Categoria::where('user_id', $userId)
        ->orderBy('nombre')
        ->get();
    
    return view('producto.index', compact('productos', 'texto', 'categoryCount', 'categorias'));
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
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $productData = $request->except('imagen_url'); // Obtenemos todo menos el archivo

        if ($request->hasFile('imagen_url')) {
            // Subir la imagen a Cloudinary y obtener el Public ID
            $uploadedFile = cloudinary()->uploadApi()->upload($request->file('imagen_url')->getRealPath(), [
                'folder' => 'productos'
            ]);
            
            // Guardamos el "Public ID" que nos da Cloudinary en la base de datos
            $productData['imagen_url'] = $uploadedFile['public_id'];
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
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $productData = $request->except('imagen_url');

        if ($request->hasFile('imagen_url')) {
            // Borramos la imagen antigua de Cloudinary si existe
            if ($producto->imagen_url) {
                cloudinary()->uploadApi()->destroy($producto->imagen_url);
            }
            
            // Subimos la nueva
           $uploadedFile = cloudinary()->uploadApi()->upload($request->file('imagen_url')->getRealPath(), [
                'folder' => 'productos'
            ]);
            $productData['imagen_url'] = $uploadedFile['public_id'];
        }


        $producto->update($productData);

        return redirect()->route('productos.index')->with('mensaje', 'Producto actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        
        // Borrar la imagen asociada
        // Borramos la imagen de Cloudinary si existe
        if ($producto->imagen_url) {
            cloudinary()->uploadApi()->destroy($producto->imagen_url);
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

      public function listarPorCategoria(Categoria $categoria)
    {
        // Usamos la relación 'productos()' que definimos en el Paso 1
        $productos = $categoria->productos()->orderBy('created_at', 'desc')->paginate(6);
        
        // Obtenemos todas las categorías para el menú de filtros
        $categorias = Categoria::orderBy('nombre')->get();
        
        // Reutilizamos la misma vista, pero pasamos la categoría actual
        return view('productos.index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'categoriaActual' => $categoria, // Pasamos la categoría seleccionada
        ]);
    }

    public function mostrarProductosPublico(User $tienda_user)
    {
        // ESTA LÍNEA AHORA FUNCIONARÁ CORRECTAMENTE
        // Usará la relación 'hasManyThrough' que definimos.
        $productos = $tienda_user->productos()
            ->orderBy('productos.created_at', 'desc') // Buena práctica: ser explícito con el nombre de la tabla
            ->paginate(6);
        
        // Esta línea ya funcionaba, porque la relación User->Categorias es directa.
        // La mejora de 'whereHas' sigue siendo válida y muy recomendable.
        $categorias = $tienda_user->categorias()
            ->whereHas('productos')
            ->orderBy('nombre')
            ->get();
        
        return view('tienda.index', [
            'tienda_user' => $tienda_user,
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }

    public function filtrarPorCategoriaPublico(User $tienda_user, Categoria $categoria)
    {
        // Esta validación es CRUCIAL y ya estaba correcta.
        // Verifica que la categoría pertenece al usuario de la tienda.
        if ($categoria->user_id !== $tienda_user->id) {
            abort(404);
        }

        // Esta parte no cambia, ya que obtiene los productos a partir de la categoría,
        // lo cual es una relación directa.
        $productos = $categoria->productos()
            ->orderBy('created_at', 'desc')
            ->paginate(6);
        
        $categorias = $tienda_user->categorias()
            ->whereHas('productos')
            ->orderBy('nombre')
            ->get();
        
        return view('tienda.index', [
            'tienda_user' => $tienda_user,
            'productos' => $productos,
            'categorias' => $categorias,
            'categoriaActual' => $categoria,
        ]);
    }

    public function buscarPublico(Request $request, User $tienda_user)
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $terminoBusqueda = $request->input('q');

        $categorias = $tienda_user->categorias()
                                ->where('nombre', 'LIKE', '%' . $terminoBusqueda . '%')
                                ->select('id', 'nombre')
                                ->limit(10)
                                ->get();
        
        return response()->json($categorias);
    }
    
}