<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth; // Importante

class Categoria extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'user_id']; // <-- Añade user_id

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // OPCIONAL PERO MUY RECOMENDADO: Global Scope
    // Esto hace que AUTOMÁTICAMENTE todas las consultas a Categoria
    // se filtren por el usuario logueado. ¡Es magia!
    protected static function boot()
    {
        parent::boot();

        // Solo aplicar el scope si hay un usuario autenticado
        if (Auth::check()) {
            static::addGlobalScope('user', function ($builder) {
                $builder->where('user_id', Auth::id());
            });
        }
    }

    public function productos()
    {
        // El nombre de la relación es 'productos' (plural)
        return $this->hasMany(Producto::class);
    }


}