<?php

namespace App\Filament\Supervisor\Resources\ClienteResource\Widgets;

use App\Models\Cliente;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClienteStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Médicos', Cliente::query()->where('tipo_cliente', 'medico')->count()),
            Stat::make('Farmacias', Cliente::query()->where('tipo_cliente', 'farmacia')->count()),
            Stat::make('Inst. Privadas', Cliente::query()->where('tipo_cliente', 'inst_privada')->count()),
            Stat::make('Inst. Públicas', Cliente::query()->where('tipo_cliente', 'inst_publica')->count()),
            Stat::make('Otros Clientes', Cliente::query()->where('tipo_cliente', 'otro')->count()),
            Stat::make('Activos', Cliente::query()->where('estado', 'activo')->count()),
            Stat::make('Inactivos', Cliente::query()->where('estado', 'inactivo')->count()),
            Stat::make('Pendientes', Cliente::query()->where('estado', 'pendiente')->count()),
        ];
    }
}
