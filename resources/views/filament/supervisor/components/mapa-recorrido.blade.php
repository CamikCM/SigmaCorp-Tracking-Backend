@php
    /** @var array $payload */
    $mapId = 'mapa-recorrido-' . \Illuminate\Support\Str::uuid();
    $points = $payload['points'] ?? [];
@endphp

<div class="space-y-3">
    <div class="rounded-lg border overflow-hidden">
        <div class="px-4 py-3 text-white" style="background: #00b3b3;">
            <div class="text-sm font-semibold">
                Usuario: <span class="font-bold">{{ $payload['usuario'] ?? '-' }}</span>
                | Regional: <span class="font-bold">{{ $payload['regional'] ?? '-' }}</span>
                | Fecha: <span class="font-bold">{{ $payload['fecha'] ?? '-' }}</span>
                | Horario: <span class="font-bold">{{ $payload['horario'] ?? '-' }}</span>
            </div>
        </div>
        <div class="px-4 py-2 bg-gray-50 text-sm">
            <div class="flex flex-wrap gap-x-6 gap-y-1">
                <div><span class="font-semibold">Puntos totales:</span> {{ $payload['total'] ?? 0 }}</div>
                <div><span class="font-semibold">Puntos reales:</span> {{ $payload['reales'] ?? 0 }}</div>
                <div><span class="font-semibold">Puntos simulados:</span> {{ $payload['simulados'] ?? 0 }}</div>
                <div><span class="font-semibold">Puntos online:</span> {{ $payload['online'] ?? 0 }}</div>
                <div><span class="font-semibold">Puntos offline:</span> {{ $payload['offline'] ?? 0 }}</div>
                <div><span class="font-semibold">Distancia recorrida:</span> {{ number_format((float) ($payload['distancia_km'] ?? 0), 2) }} km</div>
                <div><span class="font-semibold">Marcajes:</span> {{ $payload['marcajes'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 lg:col-span-9">
            <div class="rounded-lg border overflow-hidden">
                <div id="{{ $mapId }}" style="height: 520px;"></div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-3 space-y-3">
            <div class="rounded-lg border p-3 bg-white">
                <div class="text-sm font-semibold mb-2">Información del Día</div>

                <div class="text-xs font-semibold text-gray-500">MARCAJES DEL DÍA</div>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex justify-between"><span>Inicio Jornada</span><span class="font-semibold">{{ $payload['marcajes_hora']['inicio_jornada'] ?? '-' }}</span></div>
                    <div class="flex justify-between"><span>Inicio Almuerzo</span><span class="font-semibold">{{ $payload['marcajes_hora']['inicio_almuerzo'] ?? '-' }}</span></div>
                    <div class="flex justify-between"><span>Fin Almuerzo</span><span class="font-semibold">{{ $payload['marcajes_hora']['fin_almuerzo'] ?? '-' }}</span></div>
                    <div class="flex justify-between"><span>Fin Jornada</span><span class="font-semibold">{{ $payload['marcajes_hora']['fin_jornada'] ?? '-' }}</span></div>
                </div>

                <div class="mt-3 text-xs font-semibold text-gray-500">TIPOS DE TRACKING</div>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full" style="background:#22c55e"></span> Online - Real</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full" style="background:#eab308"></span> Online - Fake</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full" style="background:#3b82f6"></span> Offline - Real</div>
                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full" style="background:#ef4444"></span> Offline - Fake</div>
                </div>
            </div>

            <div class="rounded-lg border overflow-hidden bg-white">
                <div class="px-3 py-2 text-sm font-semibold border-b">Puntos ({{ count($points) }})</div>
                <div class="max-h-[520px] overflow-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-2 py-2 text-left">N°</th>
                                <th class="px-2 py-2 text-left">Hora</th>
                                <th class="px-2 py-2 text-left">Vel</th>
                                <th class="px-2 py-2 text-left">Conex</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($points as $i => $p)
                                <tr class="border-t">
                                    <td class="px-2 py-1">{{ $i + 1 }}</td>
                                    <td class="px-2 py-1">{{ $p['hora'] ?? '-' }}</td>
                                    <td class="px-2 py-1">{{ $p['vel'] !== null ? number_format((float) $p['vel'], 2) : '-' }}</td>
                                    <td class="px-2 py-1">{{ $p['conexion'] ?? 'OFF' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet (CDN) para el mapa de recorrido --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    (function () {
        const points = @json($points);
        const elId = @json($mapId);

        if (!points.length) {
            const el = document.getElementById(elId);
            if (el) {
                el.innerHTML = '<div style="padding:16px;color:#666">Sin puntos en el rango seleccionado.</div>';
            }
            return;
        }

        const first = points[0];
        const map = L.map(elId, { scrollWheelZoom: true }).setView([first.lat, first.lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const latlngs = [];

        function colorFor(p) {
            const online = (p.conexion || 'OFF') === 'ON';
            const fake = !!p.simulado;
            if (online && !fake) return '#22c55e';
            if (online && fake) return '#eab308';
            if (!online && !fake) return '#3b82f6';
            return '#ef4444';
        }

        points.forEach((p, idx) => {
            if (typeof p.lat !== 'number' || typeof p.lng !== 'number') return;
            latlngs.push([p.lat, p.lng]);
            const popup = `
                <div style="font-size:12px;line-height:1.2">
                    <div><strong>Punto #${idx + 1}</strong></div>
                    <div>Hora: ${p.hora ?? '-'}</div>
                    <div>Velocidad: ${p.vel ?? '-'} km/h</div>
                    <div>Altitud: ${p.altitud ?? '-'} m</div>
                    <div>Batería: ${p.bateria ?? '-'}%</div>
                    <div>Precisión: ${p.precision ?? '-'} m</div>
                    <div>Conexión: ${p.conexion ?? 'OFF'}</div>
                    <div>Simulado: ${p.simulado ? 'Sí' : 'No'}</div>
                </div>
            `;

            L.circleMarker([p.lat, p.lng], {
                radius: 5,
                color: colorFor(p),
                fillColor: colorFor(p),
                fillOpacity: 0.85,
                weight: 1,
            }).addTo(map).bindPopup(popup);
        });

        if (latlngs.length > 1) {
            L.polyline(latlngs, { weight: 3, opacity: 0.7 }).addTo(map);
        }

        const bounds = L.latLngBounds(latlngs);
        map.fitBounds(bounds.pad(0.2));
    })();
</script>
