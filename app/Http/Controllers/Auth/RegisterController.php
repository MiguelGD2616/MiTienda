<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;


class RegisterController extends Controller
{
    public function showRegistroForm(){
        $roles = Role::all(); // Obtener todos los roles desde la base de datos
        return view('autenticacion.registro', compact('roles'));
    }

    public function registrar(UserRequest $request){
        $usuario = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'activo' => 1,
        ]);

        $categoria = $request->input('categoria');
        $rolCategoria = Role::where('name', $categoria)->first();

        if($rolCategoria){
            $usuario->assignRole($rolCategoria);
        }

        Auth::login($usuario);
        return redirect()->route('dashboard')->with('mensaje', 'Registro exitoso. ¡Bienvenido!');
    }

}
