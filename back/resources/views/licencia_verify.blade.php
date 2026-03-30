<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Licencia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .box { max-width: 720px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; padding: 16px; }
        .ok { color: #1b5e20; }
        .bad { color: #b71c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #eee; }
        .muted { color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Verificación de Licencia</h2>
        <div class="{{ $vigente ? 'ok' : 'bad' }}">
            <b>Estado:</b> {{ $vigente ? 'VIGENTE' : 'NO VIGENTE' }}
        </div>
        <div class="muted">Fecha consulta: {{ $hoy }}</div>

        <table>
            <tr><th>N° Licencia</th><td>{{ $licencia->num_licencia }}</td></tr>
            <tr><th>Chofer</th><td>{{ $licencia->chofer?->nombre }} {{ $licencia->chofer?->apellido }}</td></tr>
            <tr><th>CI</th><td>{{ $licencia->chofer?->cedula }} {{ $licencia->chofer?->comp }}</td></tr>
            <tr><th>Placa</th><td>{{ $licencia->taxi?->placa }}</td></tr>
            <tr><th>Sindicato</th><td>{{ $licencia->sindicato?->nombre ?? '-' }}</td></tr>
            <tr><th>Vigencia desde</th><td>{{ $licencia->fecha_licencia ?? '-' }}</td></tr>
            <tr><th>Vigencia hasta</th><td>{{ $licencia->vigencia_hasta ?? '-' }}</td></tr>
            <tr><th>Estado registro</th><td>{{ strtoupper((string) ($licencia->estado ?? '-')) }}</td></tr>
        </table>
    </div>
</body>
</html>

