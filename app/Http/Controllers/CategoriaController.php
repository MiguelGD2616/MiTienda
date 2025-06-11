<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $texto=$request->input('texto');
        $registros = Auth::User()->categorias()
            ->where('nombre', 'like', '%'.$texto.'%')
            ->paginate(10); // Aumenté la paginación a 10
        return view('categoria.index', compact('registros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('categoria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                // Categoria unica
                Rule::unique('categorias')->where('user_id', Auth::id()),
            ],
            'descripcion' => 'nullable|string|max:255',
        ], [
            // Mensajes de error p
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.unique' => 'Ya tienes una categoría con este nombre.',
        ]);

        Auth::user()->categorias()->create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
        ]);

        return redirect()->route('categorias.index')
        ->with('mensaje','Categoria registrada satisfactoriamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $registros = Categoria::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('categoria.edit',compact('registros'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $registro = Categoria::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                // La regla clave para actualizar:
                // Debe ser único, PERO ignorando la propia categoría que estamos editando.
                Rule::unique('categorias')->where('user_id', Auth::id())->ignore($registro->id),
            ],
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.unique' => 'Ya tienes otra categoría con este nombre.',
        ]);

        $registro->nombre=$request->input('nombre');
        $registro->descripcion=$request->input('descripcion');
        $registro->save();
        return redirect()->route('categorias.index')
        ->with('mensaje','Registro actualizado satisfactoriamente');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $registro = Categoria::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $registro->delete();
        return redirect()->route('categorias.index')
        ->with('mensaje','Eliminado satisfactoriamente');

    }

    public function listar(Request $request){
    
        $texto = $request->input('texto');
        $registros = Categoria::where('nombre', 'like', '%'.$texto.'%')->paginate(2);
        return view('categoria.list', compact('registros'));
    }

    public function buscarPublico(Request $request, User $tienda_user)
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $terminoBusqueda = $request->input('q');

        // Iniciamos la consulta desde el usuario para buscar solo en SUS categorías.
        $categorias = $tienda_user->categorias()
                                ->where('nombre', 'LIKE', '%' . $terminoBusqueda . '%')
                                ->select('id', 'nombre')
                                ->limit(10)
                                ->get();
        
        return response()->json($categorias);
    }
}
