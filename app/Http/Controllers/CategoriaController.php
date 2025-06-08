<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $texto=$request->input('texto');
        $registros = Auth::User()->categoria()
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
        

        Auth::user()->categoria()->create([
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
}
