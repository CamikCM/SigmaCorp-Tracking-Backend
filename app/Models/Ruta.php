<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'rutas';

    protected $fillable = ['sucursal_id','nombre','descripcion'];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionRuta::class, 'ruta_id');
    }

    public function visitadoresMedicos(): BelongsToMany
    {
        return $this->belongsToMany(VisitadorMedico::class, 'asignaciones_rutas', 'ruta_id', 'visitador_medico_id')
            ->withPivot(['activo'])
            ->withTimestamps();
    }

    public function clientesPivot(): HasMany
    {
        return $this->hasMany(RutaCliente::class, 'ruta_id');
    }

    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'ruta_clientes', 'ruta_id', 'cliente_id')
            ->withPivot(['orden'])
            ->withTimestamps();
    }
}
