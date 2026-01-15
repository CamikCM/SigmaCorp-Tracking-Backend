<?php

namespace App\Filament\Supervisor\Resources\CategoriaClienteResource\Pages;

use App\Filament\Supervisor\Resources\CategoriaClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaCliente extends EditRecord
{
    protected static string $resource = CategoriaClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
