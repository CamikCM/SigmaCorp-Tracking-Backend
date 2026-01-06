<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MuestraMedica extends Model
{
    use HasFactory;

    protected $table = 'muestras_medicas';

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    // Relaciones (se activan cuando existan estos modelos)
/*
    public function inventariosSucursales(): HasMany
    {
        return $this->hasMany(InventarioSucursalMuestra::class, 'muestra_medica_id');
    }

    public function inventariosVisitadores(): HasMany
    {
        return $this->hasMany(InventarioVisitadorMuestra::class, 'muestra_medica_id');
    }

    public function operacionesItems(): HasMany
    {
        return $this->hasMany(OperacionItem::class, 'muestra_medica_id');
    }
*/
}
