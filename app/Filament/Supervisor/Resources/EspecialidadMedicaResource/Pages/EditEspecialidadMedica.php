<?php

namespace App\Filament\Supervisor\Resources\EspecialidadMedicaResource\Pages;

use App\Filament\Supervisor\Resources\EspecialidadMedicaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEspecialidadMedica extends EditRecord
{
    protected static string $resource = EspecialidadMedicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
