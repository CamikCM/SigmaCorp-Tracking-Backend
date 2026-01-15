<?php

namespace App\Filament\Supervisor\Resources;

use App\Filament\Supervisor\Resources\EspecialidadMedicaResource\Pages;
use App\Models\EspecialidadMedica;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EspecialidadMedicaResource extends Resource
{
    protected static ?string $model = EspecialidadMedica::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'SigmaCity';
    protected static ?string $navigationLabel = 'Especialidades Med.';
    protected static ?string $modelLabel = 'Especialidad Médica';
    protected static ?string $pluralModelLabel = 'Especialidades Médicas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('abreviacion')
                            ->label('Abreviación')
                            ->maxLength(20)
                            ->required(),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Especialidad')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(500),

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
                Tables\Columns\TextColumn::make('abreviacion')->label('Abreviación')->badge()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nombre')->label('Especialidad')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('descripcion')->label('Descripción')->limit(40),
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
            'index' => Pages\ListEspecialidadMedicas::route('/'),
            'create' => Pages\CreateEspecialidadMedica::route('/create'),
            'edit' => Pages\EditEspecialidadMedica::route('/{record}/edit'),
        ];
    }
}
