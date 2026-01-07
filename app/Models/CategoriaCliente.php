<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaCliente extends Model
{
    use HasFactory;

    protected $table = 'categorias_clientes';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'categoria_id');
    }

}
