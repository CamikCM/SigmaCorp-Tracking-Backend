<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EspecialidadMedica extends Model
{
    use HasFactory;

    protected $table = 'especialidades_medicas';

    protected $fillable = ['nombre', 'descripcion'];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'especialidad_id');
    }
}
