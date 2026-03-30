<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
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
    <h1>{{ $titulo }}</h1>
    <div class="meta">
        Fecha: {{ $hoy }} &nbsp;|&nbsp; Registros: {{ is_countable($licencias) ? count($licencias) : 0 }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%">N° Licencia</th>
                <th style="width: 10%">Placa</th>
                <th style="width: 22%">Chofer</th>
                <th style="width: 18%">Propietario</th>
                <th style="width: 14%">Sindicato</th>
                <th style="width: 10%">Estado</th>
                <th style="width: 14%">Vigencia hasta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($licencias as $licencia)
                <tr>
                    <td><b>{{ $licencia->num_licencia }}</b></td>
                    <td>{{ $licencia->taxi?->placa ?? '-' }}</td>
                    <td>
                        {{ $licencia->chofer?->nombre }} {{ $licencia->chofer?->apellido }}
                        <div class="muted">CI: {{ $licencia->chofer?->cedula }} {{ $licencia->chofer?->comp }}</div>
                    </td>
                    <td>
                        {{ $licencia->taxi?->propietario?->nombre }} {{ $licencia->taxi?->propietario?->apellido }}
                        <div class="muted">CI: {{ $licencia->taxi?->propietario?->cedula }} {{ $licencia->taxi?->propietario?->comp }}</div>
                    </td>
                    <td>{{ $licencia->sindicato?->nombre ?? '-' }}</td>
                    <td>{{ strtoupper((string) ($licencia->estado ?? 'VIGENTE')) }}</td>
                    <td>{{ $licencia->vigencia_hasta ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

