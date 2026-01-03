<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Location;
use App\Models\LastLocation;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use Carbon\Carbon;

class LocationController extends Controller
{
    /**
     * @OA\Post(
     *   path="/locations",
     *   tags={"Locations"},
     *   summary="Registrar una ubicación (solo lat/lng por ahora)",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"latitude","longitude"},
     *       @OA\Property(property="latitude", type="number", format="float", example=-12.046374),
     *       @OA\Property(property="longitude", type="number", format="float", example=-77.042793)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Ubicación registrada"),
     *   @OA\Response(response=422, description="Validación")
     * )
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'latitude'  => ['required','numeric','between:-90,90'],
            'longitude' => ['required','numeric','between:-180,180'],
        ]);

        $locationId = null;

        DB::transaction(function () use ($user, $data, &$locationId) {
            // 1) Guardar en histórico
            $loc = Location::create([
                'user_id'   => $user->id,
                'latitude'  => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);
            $locationId = $loc->id;

            // 2) Upsert en last_locations
            LastLocation::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'latitude'  => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]
            );
        });

        return response()->json([
            'id'                     => $locationId,
            'saved'                  => true,
            'last_location_updated'  => true,
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/locations/bulk",
     *   tags={"Locations"},
     *   summary="Registrar ubicaciones en lote (solo lat/lng por ahora)",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"items"},
     *       @OA\Property(
     *         property="items",
     *         type="array",
     *         @OA\Items(
     *           required={"latitude","longitude"},
     *           @OA\Property(property="latitude", type="number", format="float", example=-12.046374),
     *           @OA\Property(property="longitude", type="number", format="float", example=-77.042793)
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=201, description="Lote insertado"),
     *   @OA\Response(response=422, description="Validación")
     * )
     */
    public function bulk(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'items'                => ['required','array','min:1','max:100'],
            'items.*.latitude'     => ['required','numeric','between:-90,90'],
            'items.*.longitude'    => ['required','numeric','between:-180,180'],
        ]);

        $now  = now();
        $rows = collect($validated['items'])->map(function ($it) use ($user, $now) {
            return [
                'user_id'    => $user->id,
                'latitude'   => $it['latitude'],
                'longitude'  => $it['longitude'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        });

        DB::transaction(function () use ($rows, $user) {
            // Insertar en histórico
            \App\Models\Location::insert($rows->all());

            // Tomamos el ÚLTIMO del array como el más reciente (hasta añadir captured_at)
            $latest = $rows->last();

            \App\Models\LastLocation::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'latitude'  => $latest['latitude'],
                    'longitude' => $latest['longitude'],
                    'updated_at'=> now(),
                ]
            );
        });

        return response()->json([
            'message'                => 'Lecturas insertadas',
            'inserted'               => $rows->count(),
            'last_location_updated'  => true,
        ], 201);
    }

    /**
     * @OA\Get(
     *   path="/me/last-location",
     *   tags={"Locations"},
     *   summary="Última ubicación del usuario autenticado",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK"
     *   ),
     *   @OA\Response(response=404, description="No data")
     * )
     */
    public function myLast(Request $request)
    {
        $last = \App\Models\LastLocation::where('user_id', $request->user()->id)->first();

        if (!$last) {
            return response()->json(['message' => 'No data'], 404);
        }

        // Mientras no exista 'captured_at', devolvemos updated_at como captured_at
        $capturedAt = $last->captured_at ?? $last->updated_at;

        return response()->json([
            'latitude'    => (float) $last->latitude,
            'longitude'   => (float) $last->longitude,
            'captured_at' => $capturedAt?->toISOString(),
            'updated_at'  => $last->updated_at?->toISOString(),
        ]);
    }

    /**
     * @OA\Get(
     *   path="/users/{id}/last-location",
     *   tags={"Locations"},
     *   summary="Última ubicación por usuario (para panel web)",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="No data"),
     *   @OA\Response(response=422, description="Validación"),
     *   @OA\Response(response=500, description="Error interno")
     * )
     */
    public function userLast(Request $request, $id)
    {
        // Para respuestas más verbosas solo en desarrollo
        $debug = (bool) config('app.debug');

        try {
            // 1) Validar el parámetro de ruta
            $validator = Validator::make(
                ['id' => $id],
                ['id' => ['required','integer','min:1','exists:users,id']]
            );

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Parámetro inválido',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // 2) Buscar última ubicación
            $last = \App\Models\LastLocation::where('user_id', (int)$id)->first();

            if (!$last) {
                return response()->json(['message' => 'No data'], 404);
            }

            // Mientras no exista 'captured_at' en BD, usamos updated_at
            $capturedAt = $last->captured_at ?? $last->updated_at;

            return response()->json([
                'latitude'    => (float) $last->latitude,
                'longitude'   => (float) $last->longitude,
                'captured_at' => optional($capturedAt)->toISOString(),
                'updated_at'  => optional($last->updated_at)->toISOString(),
            ], 200);

        } catch (QueryException $e) {
            // Errores de base de datos
            $payload = ['message' => 'Error de base de datos'];
            if ($debug) {
                $payload['error']   = $e->getMessage();
                $payload['sql']     = $e->getSql();
                $payload['bindings']= $e->getBindings();
            }
            return response()->json($payload, 500);

        } catch (\Throwable $e) {
            // Cualquier otro error inesperado
            $payload = ['message' => 'Error interno del servidor'];
            if ($debug) {
                $payload['error'] = $e->getMessage();
                $payload['type']  = get_class($e);
            }
            return response()->json($payload, 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/users/{id}/locations",
     *   tags={"Locations"},
     *   summary="Histórico de ubicaciones paginado",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *   @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date-time")),
     *   @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date-time")),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100)),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=422, description="Validación"),
     *   @OA\Response(response=500, description="Error interno")
     * )
     */
    public function userLocations(Request $request, $id)
    {
        $debug = (bool) config('app.debug');

        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make(
                [
                    'id'       => $id,
                    'from'     => $request->query('from'),
                    'to'       => $request->query('to'),
                    'per_page' => $request->query('per_page'),
                ],
                [
                    'id'       => ['required','integer','min:1','exists:users,id'],
                    'from'     => ['nullable','date'],
                    'to'       => ['nullable','date','after_or_equal:from'],
                    'per_page' => ['nullable','integer','min:1','max:100'],
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Parámetros inválidos',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $query = \App\Models\Location::where('user_id', (int) $id);

            // Normalización de fechas:
            // - Si vienen sin hora, startOfDay/endOfDay cubren el día completo
            // - Si vienen con hora, respetamos la hora precisa
            $fromParam = $request->query('from');
            $toParam   = $request->query('to');

            $from = $fromParam ? Carbon::parse($fromParam) : null;
            $to   = $toParam   ? Carbon::parse($toParam)   : null;

            // Detecta si el string es solo fecha (YYYY-MM-DD) para aplicar día completo
            $isDateOnly = fn($s) => is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s);

            if ($from && $isDateOnly($fromParam)) {
                $from = $from->startOfDay();
            }
            if ($to && $isDateOnly($toParam)) {
                // Opción A (inclusivo): fin de día
                $to = $to->endOfDay();

                // Opción B (exclusivo, alternativa recomendada para grandes volúmenes):
                // $to = $to->addDay()->startOfDay();
                // y abajo usar < $to en lugar de <=
            }

            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]); // inclusivo
                // Para la opción B:
                // $query->where('created_at', '>=', $from)
                //       ->where('created_at', '<',  $to);
            } elseif ($from) {
                $query->where('created_at', '>=', $from);
            } elseif ($to) {
                $query->where('created_at', '<=', $to); // o '<' si aplicaste opción B
            }

            $perPage   = (int) ($request->query('per_page', 15));
            $perPage   = max(1, min(100, $perPage));

            $paginator = $query->orderByDesc('created_at')->paginate($perPage);

            $paginator->getCollection()->transform(function ($loc) {
                return [
                    'latitude'    => (float) $loc->latitude,
                    'longitude'   => (float) $loc->longitude,
                    'captured_at' => optional($loc->created_at)->toISOString(),
                ];
            });

            return response()->json($paginator, 200);

        } catch (QueryException $e) {
            $payload = ['message' => 'Error de base de datos'];
            if ($debug) {
                $payload['error']    = $e->getMessage();
                $payload['sql']      = $e->getSql();
                $payload['bindings'] = $e->getBindings();
            }
            return response()->json($payload, 500);

        } catch (\Throwable $e) {
            $payload = ['message' => 'Error interno del servidor'];
            if ($debug) {
                $payload['error'] = $e->getMessage();
                $payload['type']  = get_class($e);
            }
            return response()->json($payload, 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/locations/last",
     *   tags={"Locations"},
     *   summary="Última ubicación de todos los usuarios (para dashboard tiempo real)",
     *   security={{"sanctum":{}}},
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=500, description="Error interno")
     * )
     */
    public function lastAll(Request $request)
    {
        $debug = (bool) config('app.debug');

        try {
            // Cinturón y tirantes: si por alguna razón no pasó el middleware
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            // Consideramos “activos” a quienes tengan registro en last_locations
            $rows = \App\Models\LastLocation::query()
                ->join('users', 'users.id', '=', 'last_locations.user_id')
                ->select(
                    'last_locations.user_id',
                    'users.name',
                    'last_locations.latitude',
                    'last_locations.longitude',
                    'last_locations.updated_at'
                )
                ->orderByDesc('last_locations.updated_at')
                ->get()
                ->map(function ($r) {
                    return [
                        'user_id'   => (int) $r->user_id,
                        'name'      => (string) $r->name,
                        'latitude'  => (float) $r->latitude,
                        'longitude' => (float) $r->longitude,
                        'updated_at'=> optional($r->updated_at)->toISOString(),
                    ];
                });

            return response()->json($rows, 200);

        } catch (\Illuminate\Database\QueryException $e) {
            $payload = ['message' => 'Error de base de datos'];
            if ($debug) {
                $payload['error'] = $e->getMessage();
                $payload['sql']   = $e->getSql();
            }
            return response()->json($payload, 500);

        } catch (\Throwable $e) {
            $payload = ['message' => 'Error interno del servidor'];
            if ($debug) {
                $payload['error'] = $e->getMessage();
                $payload['type']  = get_class($e);
            }
            return response()->json($payload, 500);
        }
    }
}
