<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioVisitadorMuestra extends Model
{
    use HasFactory;

    protected $table = 'inventarios_visitadores_muestras';

    protected $fillable = ['visitador_medico_id','muestra_medica_id','cantidad'];

    protected $casts = ['cantidad' => 'integer'];

    public function visitadorMedico(): BelongsTo
    {
        return $this->belongsTo(VisitadorMedico::class, 'visitador_medico_id');
    }

    public function muestraMedica(): BelongsTo
    {
        return $this->belongsTo(MuestraMedica::class, 'muestra_medica_id');
    }
}
