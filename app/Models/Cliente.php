<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'persona_id',
        'codigo',
        'tipo_cliente',
        'categoria_id',
        'sucursal_id',
        'especialidad_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaCliente::class, 'categoria_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(EspecialidadMedica::class, 'especialidad_id');
    }

    public function rutas(): BelongsToMany
    {
        return $this->belongsToMany(Ruta::class, 'ruta_clientes', 'cliente_id', 'ruta_id')
            ->withPivot(['orden'])
            ->withTimestamps();
    }

    public function rutasPivot(): HasMany
    {
        return $this->hasMany(RutaCliente::class, 'cliente_id');
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'cliente_id');
    }
}
