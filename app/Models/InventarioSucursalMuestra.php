<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioSucursalMuestra extends Model
{
    use HasFactory;

    protected $table = 'inventarios_sucursales_muestras';

    protected $fillable = [
        'sucursal_id',
        'muestra_medica_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function muestraMedica(): BelongsTo
    {
        return $this->belongsTo(MuestraMedica::class, 'muestra_medica_id');
    }
}
