<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioVisitadorMuestra extends Model
{
    use HasFactory;

    protected $table = 'inventarios_visitadores_muestras';

    protected $fillable = [
        'usuario_id',
        'muestra_medica_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function muestraMedica(): BelongsTo
    {
        return $this->belongsTo(MuestraMedica::class, 'muestra_medica_id');
    }
}
