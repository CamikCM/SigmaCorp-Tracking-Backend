<?php

namespace App\Filament\Supervisor\Resources\CategoriaClienteResource\Pages;

use App\Filament\Supervisor\Resources\CategoriaClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriasClientes extends ListRecords
{
    protected static string $resource = CategoriaClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva Categoría'),
        ];
    }
}
