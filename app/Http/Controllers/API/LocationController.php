<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EstadoUser;
use App\Models\Jornada;
use App\Models\LastLocation;
use App\Models\Location;
use App\Models\Usuario;
use App\Models\VisitadorEstadoTracking;
use App\Models\VisitadorMedico;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    protected function resolveVisitador(Usuario $user): VisitadorMedico
    {
        return VisitadorMedico::query()
            ->where('persona_id', $user->persona_id)
            ->firstOrFail();
    }

    protected function estado(string $codigo): ?EstadoUser
    {
        return EstadoUser::query()->where('codigo', $codigo)->first();
    }

    protected function parseRecordedAt($value)
    {
        if (! $value) {
            return now();
        }

        try {
            // ISO-8601
            return \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return now();
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'speed' => ['nullable', 'numeric'],
            'heading' => ['nullable', 'numeric'],
            'altitude' => ['nullable', 'numeric'],
            'provider' => ['nullable', 'string', 'max:255'],
            'recorded_at' => ['nullable'],
        ]);

        /** @var Usuario $user */
        $user = $request->user();

        // Si no existe perfil visitador -> 403 (evitamos crear basura)
        $visitador = $this->resolveVisitador($user);

        $now = now();
        $recordedAt = $this->parseRecordedAt($data['recorded_at'] ?? null);

        return DB::transaction(function () use ($visitador, $data, $recordedAt, $now) {
            // Auto-ON + auto-jornada para compatibilidad con el tracking antiguo:
            $estadoOn = $this->estado('ON');
            if ($estadoOn && $visitador->estado_user_id !== $estadoOn->id) {
                $visitador->estado_user_id = $estadoOn->id;

                // Jornada de hoy
                $jornada = Jornada::query()->firstOrCreate(
                    ['visitador_medico_id' => $visitador->id, 'fecha' => $now->toDateString()],
                    ['inicio_jornada' => $now, 'estado' => 'abierta']
                );

                VisitadorEstadoTracking::create([
                    'visitador_medico_id' => $visitador->id,
                    'jornada_id' => $jornada->id,
                    'estado_user_id' => $estadoOn->id,
                    'tipo_marcado' => $jornada->wasRecentlyCreated ? 'inicio' : 'reanudar',
                    'fuente' => 'sistema',
                    'marcado_en' => $now,
                    'nota' => 'Auto-ON por envío de ubicación',
                ]);
            }

            $visitador->last_ping_at = $now;
            $visitador->save();

            $jornadaId = Jornada::query()
                ->where('visitador_medico_id', $visitador->id)
                ->where('fecha', $now->toDateString())
                ->where('estado', 'abierta')
                ->value('id');

            $location = Location::create([
                'visitador_medico_id' => $visitador->id,
                'jornada_id' => $jornadaId,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'accuracy' => $data['accuracy'] ?? null,
                'speed' => $data['speed'] ?? null,
                'heading' => $data['heading'] ?? null,
                'altitude' => $data['altitude'] ?? null,
                'provider' => $data['provider'] ?? null,
                'recorded_at' => $recordedAt,
            ]);

            // Upsert last_location
            $last = LastLocation::query()->updateOrCreate(
                ['visitador_medico_id' => $visitador->id],
                [
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'recorded_at' => $recordedAt,
                ]
            );

            return response()->json([
                'id' => $location->id,
                'saved' => true,
                'last_location_updated' => (bool) $last,
                'visitador_medico_id' => $visitador->id,
                'jornada_id' => $jornadaId,
            ], 201);
        });
    }

    public function bulk(Request $request)
    {
        // Compatibilidad con Flutter legado:
        // - antes enviaba { items: [...] }
        // - ahora soportamos { locations: [...] } y { items: [...] }
        $payload = $request->validate([
            'locations' => ['required_without:items', 'array', 'min:1'],
            'locations.*.latitude' => ['required_with:locations', 'numeric'],
            'locations.*.longitude' => ['required_with:locations', 'numeric'],
            'locations.*.accuracy' => ['nullable', 'numeric'],
            'locations.*.speed' => ['nullable', 'numeric'],
            'locations.*.heading' => ['nullable', 'numeric'],
            'locations.*.altitude' => ['nullable', 'numeric'],
            'locations.*.provider' => ['nullable', 'string', 'max:255'],
            'locations.*.recorded_at' => ['nullable'],

            'items' => ['required_without:locations', 'array', 'min:1'],
            'items.*.latitude' => ['required_with:items', 'numeric'],
            'items.*.longitude' => ['required_with:items', 'numeric'],
            'items.*.accuracy' => ['nullable', 'numeric'],
            'items.*.speed' => ['nullable', 'numeric'],
            'items.*.heading' => ['nullable', 'numeric'],
            'items.*.altitude' => ['nullable', 'numeric'],
            'items.*.provider' => ['nullable', 'string', 'max:255'],
            'items.*.recorded_at' => ['nullable'],
        ]);

        /** @var Usuario $user */
        $user = $request->user();
        $visitador = $this->resolveVisitador($user);

        $now = now();

        return DB::transaction(function () use ($payload, $visitador, $now) {
            // Auto-ON + auto-jornada (igual que store)
            $estadoOn = $this->estado('ON');
            $jornadaId = Jornada::query()
                ->where('visitador_medico_id', $visitador->id)
                ->where('fecha', $now->toDateString())
                ->where('estado', 'abierta')
                ->value('id');

            if (! $jornadaId) {
                $jornada = Jornada::query()->create([
                    'visitador_medico_id' => $visitador->id,
                    'fecha' => $now->toDateString(),
                    'inicio_jornada' => $now,
                    'estado' => 'abierta',
                ]);
                $jornadaId = $jornada->id;

                if ($estadoOn && $visitador->estado_user_id !== $estadoOn->id) {
                    $visitador->estado_user_id = $estadoOn->id;

                    VisitadorEstadoTracking::create([
                        'visitador_medico_id' => $visitador->id,
                        'jornada_id' => $jornadaId,
                        'estado_user_id' => $estadoOn->id,
                        'tipo_marcado' => 'inicio',
                        'fuente' => 'sistema',
                        'marcado_en' => $now,
                        'nota' => 'Auto-ON por envío bulk',
                    ]);
                }
            }

            $visitador->last_ping_at = $now;
            $visitador->save();

            $rows = [];
            $lastItem = null;

            $items = $payload['locations'] ?? $payload['items'] ?? [];

            foreach ($items as $item) {
                $recordedAt = $this->parseRecordedAt($item['recorded_at'] ?? null);

                $rows[] = [
                    'visitador_medico_id' => $visitador->id,
                    'jornada_id' => $jornadaId,
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'accuracy' => $item['accuracy'] ?? null,
                    'speed' => $item['speed'] ?? null,
                    'heading' => $item['heading'] ?? null,
                    'altitude' => $item['altitude'] ?? null,
                    'provider' => $item['provider'] ?? null,
                    'recorded_at' => $recordedAt,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $lastItem = [
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'recorded_at' => $recordedAt,
                ];
            }

            Location::query()->insert($rows);

            if ($lastItem) {
                LastLocation::query()->updateOrCreate(
                    ['visitador_medico_id' => $visitador->id],
                    $lastItem
                );
            }

            return response()->json([
                // Flutter espera este formato:
                'message' => 'Ubicaciones registradas',
                'inserted' => count($rows),
                'last_location_updated' => (bool) $lastItem,
            ], 201);
        });
    }

    public function myLast(Request $request)
    {
        /** @var Usuario $user */
        $user = $request->user();
        $visitador = $this->resolveVisitador($user);

        $row = LastLocation::query()
            ->where('visitador_medico_id', $visitador->id)
            ->first();

        $capturedAt = optional($row?->recorded_at)->toISOString();

        return response()->json([
            'visitador_medico_id' => $visitador->id,
            'latitude' => $row?->latitude,
            'longitude' => $row?->longitude,
            // compat con Flutter (LocationResult)
            'captured_at' => $capturedAt,
            // compat adicional
            'recorded_at' => $capturedAt,
            'updated_at' => optional($row?->updated_at)->toISOString(),
        ], 200);
    }

    protected function resolveVisitadorFromParam(int $id): VisitadorMedico
    {
        // 1) intenta como visitador_medico_id
        $visitador = VisitadorMedico::query()->find($id);
        if ($visitador) {
            return $visitador;
        }

        // 2) fallback: intenta como user_id (compat)
        $user = Usuario::query()->findOrFail($id);

        return VisitadorMedico::query()
            ->where('persona_id', $user->persona_id)
            ->firstOrFail();
    }

    public function userLast(Request $request, int $id)
    {
        $visitador = $this->resolveVisitadorFromParam($id);

        $row = LastLocation::query()
            ->where('visitador_medico_id', $visitador->id)
            ->first();

        $capturedAt = optional($row?->recorded_at)->toISOString();

        return response()->json([
            'visitador_medico_id' => $visitador->id,
            'latitude' => $row?->latitude,
            'longitude' => $row?->longitude,
            'captured_at' => $capturedAt,
            'recorded_at' => $capturedAt,
            'updated_at' => optional($row?->updated_at)->toISOString(),
        ], 200);
    }

    public function userLocations(Request $request, int $id)
    {
        $visitador = $this->resolveVisitadorFromParam($id);

        $perPage = (int) $request->query('per_page', 100);
        $perPage = max(1, min($perPage, 500));

        $from = $request->query('from');
        $to = $request->query('to');

        $query = Location::query()
            ->where('visitador_medico_id', $visitador->id)
            ->orderByDesc('recorded_at');

        if ($from) {
            $query->whereDate('recorded_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('recorded_at', '<=', $to);
        }

        $paginator = $query->paginate($perPage);

        // Transformamos items para que coincidan con el modelo Flutter (LocationResult)
        $paginator->getCollection()->transform(function (Location $loc) {
            $capturedAt = optional($loc->recorded_at)->toISOString();

            return [
                'latitude' => $loc->latitude !== null ? (float) $loc->latitude : null,
                'longitude' => $loc->longitude !== null ? (float) $loc->longitude : null,
                'captured_at' => $capturedAt,
                'updated_at' => optional($loc->updated_at)->toISOString(),
            ];
        });

        return response()->json($paginator, 200);
    }

    public function lastAll(Request $request)
    {
        $rows = LastLocation::query()
            ->join('visitadores_medicos as vm', 'vm.id', '=', 'last_locations.visitador_medico_id')
            ->join('persona as p', 'p.id', '=', 'vm.persona_id')
            ->join('users as u', 'u.persona_id', '=', 'p.id')
            ->leftJoin('estado_user as eu', 'eu.id', '=', 'vm.estado_user_id')
            ->leftJoin('sucursales as s', 's.id', '=', 'vm.sucursal_id')
            ->select([
                'u.id as user_id',
                'vm.id as visitador_medico_id',
                'p.nombre',
                'p.apellido_pat',
                'p.apellido_mat',
                'eu.codigo as estado',
                's.nombre as sucursal',
                'last_locations.latitude',
                'last_locations.longitude',
                'last_locations.recorded_at',
                'last_locations.updated_at',
                'vm.last_ping_at',
            ])
            ->orderByDesc('last_locations.updated_at')
            ->get()
            ->map(function ($r) {
                $nombre = trim(implode(' ', array_filter([$r->nombre, $r->apellido_pat, $r->apellido_mat])));

                return [
                    // Flutter espera user_id
                    'user_id' => (int) $r->user_id,
                    'visitador_medico_id' => (int) $r->visitador_medico_id,
                    'name' => $nombre,
                    'estado' => (string) ($r->estado ?? ''),
                    'sucursal' => (string) ($r->sucursal ?? ''),
                    'latitude' => $r->latitude !== null ? (float) $r->latitude : null,
                    'longitude' => $r->longitude !== null ? (float) $r->longitude : null,
                    // Flutter usa updated_at (y NO lee recorded_at), pero lo dejamos por si acaso
                    'recorded_at' => optional($r->recorded_at)->toISOString(),
                    'updated_at' => optional($r->updated_at)->toISOString(),
                    'last_ping_at' => optional($r->last_ping_at)->toISOString(),
                ];
            });

        return response()->json($rows, 200);
    }
}
