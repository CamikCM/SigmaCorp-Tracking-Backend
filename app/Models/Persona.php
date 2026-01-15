<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'persona';

    protected $fillable = [
        'nombre',
        'apellido_pat',
        'apellido_mat',
        'telefono_principal',
        'telefono_secundario',
        'email_personal',
        'direccion',
        'habilitado',
        'carnet_identidad',
        'foto_url',
    ];

    protected $casts = [
        'habilitado' => 'boolean',
    ];

    protected $appends = [
        'nombre_completo',
    ];

    public function usuario(): HasOne
    {
        return $this->hasOne(Usuario::class, 'persona_id');
    }

    public function visitadorMedico(): HasOne
    {
        return $this->hasOne(VisitadorMedico::class, 'persona_id');
    }

    public function supervisor(): HasOne
    {
        return $this->hasOne(Supervisor::class, 'persona_id');
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'persona_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        $parts = array_filter([
            $this->nombre,
            $this->apellido_pat,
            $this->apellido_mat,
        ]);

        return trim(implode(' ', $parts));
    }
}
