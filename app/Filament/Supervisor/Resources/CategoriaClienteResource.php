<?php

namespace App\Filament\Supervisor\Resources;

use App\Filament\Supervisor\Resources\CategoriaClienteResource\Pages;
use App\Models\CategoriaCliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriaClienteResource extends Resource
{
    protected static ?string $model = CategoriaCliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'SigmaCity';
    protected static ?string $navigationLabel = 'Categorías';
    protected static ?string $modelLabel = 'Categoría';
    protected static ?string $pluralModelLabel = 'Categorías';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\TextInput::make('codigo')
                        ->label('Código')
                        ->maxLength(30)
                        ->required(),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Categoría')
                        ->maxLength(255)
                        ->required(),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(3)
                        ->maxLength(500),

                    Forms\Components\Select::make('tipo_cliente')
                        ->label('Tipo Cliente')
                        ->options([
                            'medico' => 'médico',
                            'farmacia' => 'farmacia',
                            'inst_privada' => 'inst. privada',
                            'inst_publica' => 'inst. pública',
                            'otro' => 'otro',
                        ])
                        ->default('medico')
                        ->required(),

                    Forms\Components\TextInput::make('orden')
                        ->label('Orden')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('activo')
                        ->label('Activo')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('codigo')->label('Código')->badge()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nombre')->label('Categoría')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('descripcion')->label('Descripción')->limit(40),
                Tables\Columns\TextColumn::make('tipo_cliente')->label('Tipo Cliente')->badge()->sortable(),
                Tables\Columns\TextColumn::make('orden')->label('Orden')->sortable(),
                Tables\Columns\IconColumn::make('activo')->label('Estado')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('orden');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoriasClientes::route('/'),
            'create' => Pages\CreateCategoriaCliente::route('/create'),
            'edit' => Pages\EditCategoriaCliente::route('/{record}/edit'),
        ];
    }
}
