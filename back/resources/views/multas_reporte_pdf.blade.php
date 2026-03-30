<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE DE MULTAS</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { margin: 0; font-size: 16px; }
        .meta { margin-top: 6px; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; vertical-align: top; }
        th { background: #f5f5f5; text-align: left; }
        .right { text-align: right; }
        .muted { color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <h1>REPORTE DE MULTAS</h1>
    <div class="meta">
        Fecha impresión: {{ $hoy }} &nbsp;|&nbsp;
        Rango: {{ $ini }} al {{ $fin }} &nbsp;|&nbsp;
        Registros: {{ is_countable($multas) ? count($multas) : 0 }}
        @if(!empty($placa))
            &nbsp;|&nbsp; Placa: {{ strtoupper((string) $placa) }}
        @endif
        @if(!empty($cedula_conductor))
            &nbsp;|&nbsp; CI Conductor: {{ $cedula_conductor }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%">Fecha/Hora</th>
                <th style="width: 8%">Placa</th>
                <th style="width: 12%">Licencia</th>
                <th style="width: 18%">Propietario</th>
                <th style="width: 18%">Conductor</th>
                <th style="width: 18%">Sanción</th>
                <th style="width: 6%" class="right">Monto</th>
                <th style="width: 8%">Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($multas as $m)
                <tr>
                    <td>{{ optional($m->fecha_hora)->format('Y-m-d H:i') }}</td>
                    <td><b>{{ $m->placa ?? '-' }}</b></td>
                    <td>{{ $m->num_licencia ?? '-' }}</td>
                    <td>
                        {{ $m->taxi?->propietario?->nombre }} {{ $m->taxi?->propietario?->apellido }}
                        <div class="muted">CI: {{ $m->taxi?->propietario?->cedula }} {{ $m->taxi?->propietario?->comp }}</div>
                    </td>
                    <td>
                        {{ $m->licencia?->chofer?->nombre }} {{ $m->licencia?->chofer?->apellido }}
                        <div class="muted">CI: {{ $m->licencia?->chofer?->cedula }} {{ $m->licencia?->chofer?->comp }}</div>
                    </td>
                    <td>
                        <b>{{ $m->sancion?->tipo ?? '-' }}</b>
                        <div class="muted">{{ $m->sancion?->descripcion ?? '' }}</div>
                    </td>
                    <td class="right">{{ number_format((float) ($m->sancion?->monto ?? 0), 2) }}</td>
                    <td>{{ $m->user?->nombre ?? $m->user?->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

