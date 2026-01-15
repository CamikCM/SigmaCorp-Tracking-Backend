<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperacionItem extends Model
{
    use HasFactory;

    protected $table = 'operaciones_items';

    protected $fillable = ['operacion_id','muestra_medica_id','cantidad'];

    protected $casts = ['cantidad' => 'integer'];

    public function operacion(): BelongsTo
    {
        return $this->belongsTo(Operacion::class, 'operacion_id');
    }

    public function muestraMedica(): BelongsTo
    {
        return $this->belongsTo(MuestraMedica::class, 'muestra_medica_id');
    }
}
