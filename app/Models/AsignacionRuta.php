<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionRuta extends Model
{
    use HasFactory;

    protected $table = 'asignaciones_rutas';

    protected $fillable = ['ruta_id','visitador_medico_id','activo'];

    protected $casts = ['activo' => 'boolean'];

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    public function visitadorMedico(): BelongsTo
    {
        return $this->belongsTo(VisitadorMedico::class, 'visitador_medico_id');
    }
}
