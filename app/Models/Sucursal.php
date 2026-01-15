<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'ciudad',
        'direccion',
    ];

    public function visitadoresMedicos(): HasMany
    {
        return $this->hasMany(VisitadorMedico::class, 'sucursal_id');
    }

    public function supervisores(): HasMany
    {
        return $this->hasMany(Supervisor::class, 'sucursal_id');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'sucursal_id');
    }

    public function rutas(): HasMany
    {
        return $this->hasMany(Ruta::class, 'sucursal_id');
    }

    public function inventariosMuestras(): HasMany
    {
        return $this->hasMany(InventarioSucursalMuestra::class, 'sucursal_id');
    }

    public function operaciones(): HasMany
    {
        return $this->hasMany(Operacion::class, 'sucursal_id');
    }
}
