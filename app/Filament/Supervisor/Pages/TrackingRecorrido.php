<?php

namespace App\Filament\Supervisor\Pages;

use App\Models\Jornada;
use App\Models\Location;
use App\Models\Sucursal;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrackingRecorrido extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Tracking';
    protected static ?string $navigationLabel = 'Recorrido';
    protected static ?string $title = 'TRACKING / Recorrido';

    protected static string $view = 'filament.supervisor.pages.tracking-recorrido';

    /**
     * Estado del formulario de filtros.
     */
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'fecha' => now()->toDateString(),
            'hora_desde' => '07:00',
            'hora_hasta' => '20:00',
            'sucursal_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filtro de búsqueda')
                    ->schema([
                        DatePicker::make('fecha')
                            ->label('Fecha')
                            ->native(false)
                            ->required()
                            ->live(),

                        TimePicker::make('hora_desde')
                            ->label('Hora desde')
                            ->seconds(false)
                            ->required()
                            ->live(),

                        TimePicker::make('hora_hasta')
                            ->label('Hora hasta')
                            ->seconds(false)
                            ->required()
                            ->live(),

                        Select::make('sucursal_id')
                            ->label('Regional')
                            ->options(fn () => Sucursal::query()->orderBy('nombre')->pluck('nombre', 'id')->all())
                            ->searchable()
                            ->placeholder('Todas')
                            ->live(),
                    ])
                    ->columns(4),
            ])
            ->statePath('data');
    }

    protected function getTableQuery(): Builder
    {
        [$from, $to, $fecha] = $this->getRango();
        $sucursalId = $this->data['sucursal_id'] ?? null;

        return User::query()
            ->with('sucursal')
            ->with([
                'jornadas' => fn (Builder $q) => $q->whereDate('fecha', $fecha->toDateString()),
            ])
            // Si ya tienes roles, limita a visitadores:
            // ->role('visitador')
            ->when($sucursalId, fn (Builder $q) => $q->where('sucursal_id', $sucursalId))
            ->withCount([
                'locations as tracking_count' => fn (Builder $q) => $q
                    ->whereBetween('created_at', [$from, $to]),
                'locations as fakes_count' => fn (Builder $q) => $q
                    ->whereBetween('created_at', [$from, $to])
                    ->where('es_simulado', true),
            ])
            ->orderBy('name');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('usuario')
                    ->label('Login')
                    ->state(fn (User $record) => $record->usuario ?: (string) $record->id)
                    ->searchable(),

                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->toggleable(),

                TextColumn::make('tracking_count')
                    ->label('Tracking')
                    ->alignCenter(),

                TextColumn::make('fakes_count')
                    ->label('Fakes')
                    ->alignCenter(),

                TextColumn::make('inicio_jornada')
                    ->label('Inicio Jornada')
                    ->state(fn (User $record) => $this->jornadaDelDia($record)?->inicio_real)
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('inicio_almuerzo')
                    ->label('Inicio Almuerzo')
                    ->state(fn (User $record) => $this->jornadaDelDia($record)?->inicio_almuerzo_real)
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('fin_almuerzo')
                    ->label('Fin Almuerzo')
                    ->state(fn (User $record) => $this->jornadaDelDia($record)?->fin_almuerzo_real)
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('fin_jornada')
                    ->label('Fin Jornada')
                    ->state(fn (User $record) => $this->jornadaDelDia($record)?->fin_real)
                    ->dateTime('H:i:s')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('trabajado')
                    ->label('Trabajado')
                    ->state(fn (User $record) => $this->calcularTrabajado($record))
                    ->badge(),

                TextColumn::make('especiales')
                    ->label('Especiales')
                    ->state('-')
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->actions([
                Action::make('mapa')
                    ->label('')
                    ->icon('heroicon-o-map-pin')
                    ->tooltip('Mapa')
                    ->modalHeading('Mapa de Recorrido')
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn (User $record) => view('filament.supervisor.components.mapa-recorrido', [
                        'payload' => $this->buildMapaPayload($record),
                    ])),
            ])
            ->emptyStateHeading('Sin resultados')
            ->striped();
    }

    private function getRango(): array
    {
        $fecha = Carbon::parse($this->data['fecha'] ?? now()->toDateString());
        $desde = (string) ($this->data['hora_desde'] ?? '07:00');
        $hasta = (string) ($this->data['hora_hasta'] ?? '20:00');

        $from = Carbon::parse($fecha->toDateString() . ' ' . $desde);
        $to = Carbon::parse($fecha->toDateString() . ' ' . $hasta);

        if ($to->lessThanOrEqualTo($from)) {
            // Si el usuario pone un rango inválido, forzamos +1h para evitar errores.
            $to = (clone $from)->addHour();
        }

        return [$from, $to, $fecha];
    }

    private function jornadaDelDia(User $record): ?Jornada
    {
        // Se precarga en getTableQuery() con whereDate(fecha, ...)
        return $record->jornadas->first();
    }

    private function calcularTrabajado(User $record): string
    {
        $j = $this->jornadaDelDia($record);
        if (!$j?->inicio_real || !$j?->fin_real) {
            return '???';
        }

        $inicio = Carbon::parse($j->inicio_real);
        $fin = Carbon::parse($j->fin_real);
        $diff = $inicio->diffInSeconds($fin);

        if ($j->inicio_almuerzo_real && $j->fin_almuerzo_real) {
            $almIni = Carbon::parse($j->inicio_almuerzo_real);
            $almFin = Carbon::parse($j->fin_almuerzo_real);
            $diff -= $almIni->diffInSeconds($almFin);
        }

        $diff = max(0, $diff);

        return gmdate('H:i:s', $diff);
    }

    private function buildMapaPayload(User $record): array
    {
        [$from, $to, $fecha] = $this->getRango();

        $points = Location::query()
            ->where('user_id', $record->id)
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->limit(5000)
            ->get([
                'id',
                'latitude',
                'longitude',
                'altitud',
                'bateria',
                'velocidad',
                'precision',
                'conexion',
                'es_simulado',
                'registrado_en',
                'created_at',
            ])
            ->map(function (Location $loc) {
                $capturedAt = $loc->registrado_en ?: $loc->created_at;

                return [
                    'id' => $loc->id,
                    'lat' => (float) $loc->latitude,
                    'lng' => (float) $loc->longitude,
                    'hora' => optional($capturedAt)->format('H:i:s'),
                    'vel' => $loc->velocidad !== null ? (float) $loc->velocidad : null,
                    'precision' => $loc->precision !== null ? (float) $loc->precision : null,
                    'conexion' => $loc->conexion ?: 'OFF',
                    'simulado' => (bool) $loc->es_simulado,
                    'bateria' => $loc->bateria,
                    'altitud' => $loc->altitud,
                ];
            })
            ->values()
            ->all();

        $total = count($points);
        $simulados = collect($points)->where('simulado', true)->count();
        $reales = $total - $simulados;
        $online = collect($points)->where('conexion', 'ON')->count();
        $offline = $total - $online;
        $distancia = self::distanciaKm($points);

        $j = Jornada::query()
            ->where('usuario_id', $record->id)
            ->whereDate('fecha', $fecha->toDateString())
            ->latest('id')
            ->first();

        $marcajes = 0;
        foreach (['inicio_real', 'inicio_almuerzo_real', 'fin_almuerzo_real', 'fin_real'] as $k) {
            if ($j?->$k) {
                $marcajes++;
            }
        }

        return [
            'usuario' => trim($record->name . ' ' . ($record->apellidos ?? '')),
            'regional' => $record->sucursal?->nombre,
            'fecha' => $fecha->format('d/m/Y'),
            'horario' => ($this->data['hora_desde'] ?? '07:00') . ' - ' . ($this->data['hora_hasta'] ?? '20:00'),
            'total' => $total,
            'reales' => $reales,
            'simulados' => $simulados,
            'online' => $online,
            'offline' => $offline,
            'distancia_km' => $distancia,
            'marcajes' => $marcajes,
            'marcajes_hora' => [
                'inicio_jornada' => $j?->inicio_real ? Carbon::parse($j->inicio_real)->format('H:i') : null,
                'inicio_almuerzo' => $j?->inicio_almuerzo_real ? Carbon::parse($j->inicio_almuerzo_real)->format('H:i') : null,
                'fin_almuerzo' => $j?->fin_almuerzo_real ? Carbon::parse($j->fin_almuerzo_real)->format('H:i') : null,
                'fin_jornada' => $j?->fin_real ? Carbon::parse($j->fin_real)->format('H:i') : null,
            ],
            'points' => $points,
        ];
    }

    /**
     * Distancia recorrida aproximada en km (Haversine).
     */
    private static function distanciaKm(array $points): float
    {
        $km = 0.0;
        for ($i = 1; $i < count($points); $i++) {
            $a = $points[$i - 1];
            $b = $points[$i];
            $km += self::haversineKm($a['lat'], $a['lng'], $b['lat'], $b['lng']);
        }
        return round($km, 2);
    }

    private static function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}
