<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EstadoUser;
use App\Models\Jornada;
use App\Models\Usuario;
use App\Models\VisitadorEstadoTracking;
use App\Models\VisitadorMedico;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected function resolveVisitador(Usuario $user): VisitadorMedico
    {
        return VisitadorMedico::query()
            ->where('persona_id', $user->persona_id)
            ->firstOrFail();
    }

    protected function getEstado(string $codigo): EstadoUser
    {
        return EstadoUser::query()->where('codigo', $codigo)->firstOrFail();
    }

    public function status(Request $request)
    {
        $visitador = $this->resolveVisitador($request->user());

        return response()->json([
            'visitador_medico_id' => $visitador->id,
            'estado' => $visitador->estadoUser?->codigo,
            'last_ping_at' => optional($visitador->last_ping_at)->toISOString(),
        ], 200);
    }

    public function on(Request $request)
    {
        $user = $request->user();
        $visitador = $this->resolveVisitador($user);

        $estadoOn = $this->getEstado('ON');
        $now = now();

        // Jornada de hoy
        $jornada = Jornada::query()->firstOrCreate(
            ['visitador_medico_id' => $visitador->id, 'fecha' => $now->toDateString()],
            ['inicio_jornada' => $now, 'estado' => 'abierta']
        );

        $tipo = $jornada->wasRecentlyCreated ? 'inicio' : 'reanudar';

        if ($visitador->estado_user_id !== $estadoOn->id) {
            $visitador->estado_user_id = $estadoOn->id;
        }
        $visitador->last_ping_at = $now;
        $visitador->save();

        VisitadorEstadoTracking::create([
            'visitador_medico_id' => $visitador->id,
            'jornada_id' => $jornada->id,
            'estado_user_id' => $estadoOn->id,
            'tipo_marcado' => $tipo,
            'fuente' => 'app',
            'marcado_en' => $now,
            'nota' => null,
        ]);

        return response()->json([
            'ok' => true,
            'estado' => 'ON',
            'jornada_id' => $jornada->id,
        ], 200);
    }

    public function off(Request $request)
    {
        $user = $request->user();
        $visitador = $this->resolveVisitador($user);

        $estadoOff = $this->getEstado('OFF');
        $now = now();

        $jornada = Jornada::query()
            ->where('visitador_medico_id', $visitador->id)
            ->where('fecha', $now->toDateString())
            ->where('estado', 'abierta')
            ->first();

        $visitador->estado_user_id = $estadoOff->id;
        $visitador->last_ping_at = $now;
        $visitador->save();

        VisitadorEstadoTracking::create([
            'visitador_medico_id' => $visitador->id,
            'jornada_id' => $jornada?->id,
            'estado_user_id' => $estadoOff->id,
            'tipo_marcado' => 'pausa',
            'fuente' => 'app',
            'marcado_en' => $now,
            'nota' => null,
        ]);

        return response()->json([
            'ok' => true,
            'estado' => 'OFF',
            'jornada_id' => $jornada?->id,
        ], 200);
    }
}
