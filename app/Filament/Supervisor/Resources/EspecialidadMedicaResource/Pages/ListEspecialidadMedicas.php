<?php

namespace App\Filament\Supervisor\Resources\EspecialidadMedicaResource\Pages;

use App\Filament\Supervisor\Resources\EspecialidadMedicaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEspecialidadMedicas extends ListRecords
{
    protected static string $resource = EspecialidadMedicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva Especialidad'),
        ];
    }
}
