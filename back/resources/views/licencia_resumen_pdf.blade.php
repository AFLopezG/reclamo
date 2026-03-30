<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia - {{ $licencia->num_licencia }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; margin: 0 0 10px 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 6px; border: 1px solid #ddd; }
        th { text-align: left; background: #f5f5f5; }
        .qr { text-align: center; }
        .qr img { width: 120px; height: 120px; }
        .small { font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>DATOS DE LICENCIA</h1>

    <table>
        <tr>
            <th>N° Licencia</th>
            <td>{{ $licencia->num_licencia }}</td>
            <th>Estado</th>
            <td>{{ strtoupper((string) ($licencia->estado ?? 'VIGENTE')) }}</td>
        </tr>
        <tr>
            <th>Chofer</th>
            <td colspan="3">{{ $licencia->chofer?->nombre }} {{ $licencia->chofer?->apellido }} (CI: {{ $licencia->chofer?->cedula }} {{ $licencia->chofer?->comp }})</td>
        </tr>
        <tr>
            <th>Taxi (Placa)</th>
            <td>{{ $licencia->taxi?->placa }}</td>
            <th>Sindicato</th>
            <td>{{ $licencia->sindicato?->nombre ?? '-' }}</td>
        </tr>
        <tr>
            <th>Vigencia desde</th>
            <td>{{ $licencia->fecha_licencia ?? '-' }}</td>
            <th>Vigencia hasta</th>
            <td>{{ $licencia->vigencia_hasta ?? '-' }}</td>
        </tr>
    </table>

    <div style="margin-top: 14px" class="qr">
        <div><b>QR de verificación</b></div>
        <img src="data:image/png;base64, {!! $qrcode !!}" alt="QR">
        <div class="small">{{ $verifyUrl }}</div>
    </div>
</body>
</html>

