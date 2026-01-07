<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'codigo',
        'abreviatura',
        'nombre',
        'especialidad',
        'descripcion',
        'direccion',
        'latitud',
        'longitud',
        'categoria_id',
        'activo',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaCliente::class, 'categoria_id');
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'cliente_id');
    }

    public function rutas(): BelongsToMany
    {
        // Pivot: ruta_clientes (cliente_id, ruta_id, orden)
        return $this->belongsToMany(Ruta::class, 'ruta_clientes', 'cliente_id', 'ruta_id')
            ->withPivot('orden')
            ->withTimestamps();
    }

}

