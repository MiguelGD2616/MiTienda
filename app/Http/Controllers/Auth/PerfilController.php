<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class PerfilController extends Controller
{
    /**
     * Muestra el formulario de perfil del usuario autenticado.
     * Redirige a la vista correcta según el rol.
     */
    public function edit()
    {
        $user = Auth::user()->load('empresa', 'cliente'); // 1. Carga las relaciones para eficiencia

        if ($user->hasRole(['super_admin', 'admin'])) {
            // Un admin ve su perfil dentro del panel de administración.
            // La vista recibe el 'user' y su 'empresa' a través de la relación.
            return view('autenticacion.perfil', ['registro' => $user, 'empresa' => $user->empresa]);
        }
        
        if ($user->hasRole('cliente')) {
            // Un cliente ve su perfil en una vista pública o de cliente.
            // La vista recibe el 'user' y su perfil 'cliente' a través de la relación.
            return view('autenticacion.cliente', ['registro' => $user, 'cliente' => $user->cliente]);
        }

        // Si el usuario no tiene un rol con perfil editable, lo redirigimos.
        return redirect()->route('dashboard')->with('error', 'No tienes un perfil de usuario válido para editar.');
    }

    /**
     * Actualiza el perfil del usuario autenticado.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // --- VALIDACIÓN DE DATOS ---
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ];
        
        if ($user->hasRole(['super_admin', 'admin']) && $user->empresa) {
            $rules['empresa_nombre'] = ['required', 'string', 'max:255', Rule::unique('empresas', 'nombre')->ignore($user->empresa_id)];
            $rules['empresa_rubro'] = 'nullable|string|max:255';
            $rules['empresa_telefono_whatsapp'] = 'nullable|string|max:20';
            $rules['empresa_logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }
        elseif ($user->hasRole('cliente')) {
            $rules['cliente_telefono'] = 'nullable|string|max:20';
        }
        
        $validatedData = $request->validate($rules);

        // --- ACTUALIZACIÓN DE DATOS ---
        // Usamos una transacción por si la actualización de la empresa falla.
        DB::beginTransaction();
        try {
            // 2. Actualizar el modelo User (común para todos)
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            if ($request->filled('password')) {
                $user->password = Hash::make($validatedData['password']);
            }
            $user->save();

            // 3. Actualizar la Empresa si es un Admin
            if ($user->hasRole(['super_admin', 'admin']) && $user->empresa) {
                $user->empresa->update([
                    'nombre' => $validatedData['empresa_nombre'],
                    'rubro' => $request->input('empresa_rubro'),
                    'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
                ]);
                
                if ($request->hasFile('empresa_logo')) {
                    // Lógica para borrar logo antiguo y subir el nuevo...
                }
            } 
            // 4. Actualizar el Cliente si es un Cliente
            elseif ($user->hasRole('cliente') && $user->cliente) {
                $user->cliente->update([
                    'nombre' => $validatedData['name'], // Sincronizamos el nombre
                    'telefono' => $request->input('cliente_telefono'),
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al actualizar el perfil: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('perfil.edit')->with('mensaje', 'Perfil actualizado correctamente.');
    }
}