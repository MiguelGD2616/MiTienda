<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['nombre', 'slug', 'rubro','telefono_whatsapp'];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_empresa');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
