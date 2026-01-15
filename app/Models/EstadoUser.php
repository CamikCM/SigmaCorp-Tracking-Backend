<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoUser extends Model
{
    use HasFactory;

    protected $table = 'estado_user';

    protected $fillable = [
        'codigo',
        'nombre',
    ];

    public function visitadoresMedicos(): HasMany
    {
        return $this->hasMany(VisitadorMedico::class, 'estado_user_id');
    }

    public function eventosTracking(): HasMany
    {
        return $this->hasMany(VisitadorEstadoTracking::class, 'estado_user_id');
    }
}
