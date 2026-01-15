<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitadorEstadoTracking extends Model
{
    use HasFactory;

    protected $table = 'visitador_estado_tracking';

    protected $fillable = [
        'visitador_medico_id',
        'jornada_id',
        'estado_user_id',
        'tipo_marcado',
        'fuente',
        'marcado_en',
        'nota',
    ];

    protected $casts = [
        'marcado_en' => 'datetime',
    ];

    public function visitadorMedico(): BelongsTo
    {
        return $this->belongsTo(VisitadorMedico::class, 'visitador_medico_id');
    }

    public function jornada(): BelongsTo
    {
        return $this->belongsTo(Jornada::class, 'jornada_id');
    }

    public function estadoUser(): BelongsTo
    {
        return $this->belongsTo(EstadoUser::class, 'estado_user_id');
    }
}
