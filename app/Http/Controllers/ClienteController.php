<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = User::role('cliente')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    public function show(User $cliente)
    {
        if (!$cliente->hasRole('cliente')) {
            abort(404);
        }

        return view('clientes.show', compact('cliente'));
    }

    public function destroy(User $cliente)
    {
        if (!$cliente->hasRole('cliente')) {
            abort(403);
        }

        $cliente->delete();
        return redirect()->route('clientes.index')->with('mensaje', 'Cliente eliminado');
    }
}
