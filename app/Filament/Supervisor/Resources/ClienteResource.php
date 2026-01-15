<?php

namespace App\Filament\Supervisor\Resources;

use App\Filament\Supervisor\Resources\ClienteResource\Pages;
use App\Filament\Supervisor\Resources\ClienteResource\Widgets\ClienteStats;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'SigmaCity';
    protected static ?string $navigationLabel = 'Gestión de Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del Cliente')
                    ->schema([
                        Forms\Components\Select::make('tipo_cliente')
                            ->label('Tipo')
                            ->options([
                                'medico' => 'Médico',
                                'farmacia' => 'Farmacia',
                                'inst_privada' => 'Inst. Privada',
                                'inst_publica' => 'Inst. Pública',
                                'otro' => 'Otros',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->maxLength(50)
                            ->required(),

                        Forms\Components\TextInput::make('abreviatura')
                            ->label('Abreviatura')
                            ->maxLength(30),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre/Razón Social')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\TextInput::make('nit_ci')
                            ->label('NIT/CI')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('nombre_comercial')
                            ->label('Nombre Comercial')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('grupo_institucion')
                            ->label('Grupo/Institución')
                            ->maxLength(255),

                        Forms\Components\Select::make('categoria_id')
                            ->label('Categoría')
                            ->relationship('categoria', 'nombre')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('especialidad_id')
                            ->label('Especialidad')
                            ->relationship('especialidadMedica', 'nombre')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('sucursal_id')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'activo' => 'Activo',
                                'inactivo' => 'Inactivo',
                                'pendiente' => 'Pendiente',
                            ])
                            ->default('activo')
                            ->required(),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('telefono_principal')
                            ->label('Teléfono Principal')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('telefono_celular')
                            ->label('Teléfono Celular')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\TextInput::make('latitud')
                            ->label('Latitud')
                            ->numeric(),

                        Forms\Components\TextInput::make('longitud')
                            ->label('Longitud')
                            ->numeric(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('SigmaCity')
                    ->schema([
                        Forms\Components\TextInput::make('codigo_zona')
                            ->label('Código Zona')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('cliente_ovp')
                            ->label('Cliente OVP')
                            ->maxLength(150),

                        Forms\Components\DatePicker::make('fecha_nacimiento')
                            ->label('Fecha Nacimiento/Creación'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('tipo_cliente')->label('Tipo')->badge()->sortable(),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre/Razón Social')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('categoria.nombre')->label('Categoría')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('especialidadMedica.nombre')->label('Especialidad')->toggleable(),
                Tables\Columns\TextColumn::make('nit_ci')->label('NIT/CI')->toggleable(),
                Tables\Columns\TextColumn::make('telefono_celular')->label('Contacto')->toggleable(),
                Tables\Columns\TextColumn::make('sucursal.nombre')->label('Sucursal')->toggleable(),
                Tables\Columns\TextColumn::make('estado')->label('Estado')->badge()->sortable(),
                Tables\Columns\IconColumn::make('activo')->label('Activo')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha Reg.')->date('d/m/Y')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_cliente')
                    ->label('Tipo')
                    ->options([
                        'medico' => 'Médico',
                        'farmacia' => 'Farmacia',
                        'inst_privada' => 'Inst. Privada',
                        'inst_publica' => 'Inst. Pública',
                        'otro' => 'Otros',
                    ]),
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                        'pendiente' => 'Pendiente',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make('ver')
                    ->label('')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()->label('')->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()->label('')->icon('heroicon-o-trash'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(2)
                    ->schema([
                        Infolists\Components\Section::make('Información Básica')
                            ->schema([
                                Infolists\Components\TextEntry::make('nombre')->label('Nombre'),
                                Infolists\Components\TextEntry::make('tipo_cliente')->label('Tipo'),
                                Infolists\Components\TextEntry::make('nit_ci')->label('NIT/CI')->placeholder('-'),
                                Infolists\Components\TextEntry::make('nombre_comercial')->label('Nombre Comercial')->placeholder('-'),
                                Infolists\Components\TextEntry::make('grupo_institucion')->label('Grupo/Institución')->placeholder('-'),
                                Infolists\Components\TextEntry::make('estado')->label('Estado'),
                                Infolists\Components\TextEntry::make('created_at')->label('Fecha Registro')->dateTime('d/m/Y H:i')->placeholder('-'),
                            ]),

                        Infolists\Components\Section::make('Información Profesional')
                            ->schema([
                                Infolists\Components\TextEntry::make('especialidadMedica.nombre')->label('Especialidad')->placeholder('No aplica'),
                                Infolists\Components\TextEntry::make('categoria.nombre')->label('Categoría')->placeholder('-'),
                                Infolists\Components\TextEntry::make('sucursal.nombre')->label('Sucursal')->placeholder('-'),
                                Infolists\Components\TextEntry::make('codigo_zona')->label('Código Zona')->placeholder('-'),
                                Infolists\Components\TextEntry::make('cliente_ovp')->label('Cliente OVP')->placeholder('-'),
                                Infolists\Components\TextEntry::make('fecha_nacimiento')->label('Fecha Nacimiento/Creación')->date('d/m/Y')->placeholder('-'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Información de Contacto')
                    ->schema([
                        Infolists\Components\TextEntry::make('telefono_principal')->label('Teléfono Principal')->placeholder('-'),
                        Infolists\Components\TextEntry::make('telefono_celular')->label('Teléfono Celular')->placeholder('-'),
                        Infolists\Components\TextEntry::make('email')->label('Email')->placeholder('-'),
                        Infolists\Components\TextEntry::make('direccion')->label('Dirección')->placeholder('-'),
                        Infolists\Components\TextEntry::make('coordenadas')->label('Coordenadas')
                            ->state(fn (Cliente $record) => $record->latitud && $record->longitud ? ($record->latitud . ', ' . $record->longitud) : null)
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ClienteStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'view' => Pages\ViewCliente::route('/{record}'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
