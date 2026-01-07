<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'rutas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'sucursal_id',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function clientesRuta(): HasMany
    {
        // RutaCliente se crea en otro issue; por ahora queda la relación lista
        return $this->hasMany(RutaCliente::class, 'ruta_id');
    }

    public function asignaciones(): HasMany
    {
        // AsignacionRuta se crea en otro issue
        return $this->hasMany(AsignacionRuta::class, 'ruta_id');
    }

    public function visitas(): HasMany
    {
        // Visita se crea en otro issue
        return $this->hasMany(Visita::class, 'ruta_id');
    }
}
