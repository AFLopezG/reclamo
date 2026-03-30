<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credencial - {{ $licencia->num_licencia }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .page { width: 100%; }
        .card {
            width: 86mm;
            height: 54mm;
            border: 1px solid #222;
            border-radius: 8px;
            padding: 8px;
            box-sizing: border-box;
        }
        .row { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; }
        .col-left { width: 65%; }
        .col-right { width: 35%; text-align: right; }
        .title { font-weight: bold; font-size: 13px; margin-bottom: 4px; }
        .label { color: #555; font-size: 10px; }
        .value { font-weight: bold; }
        .qr { width: 90px; height: 90px; }
        .small { font-size: 9px; color: #666; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; color: #fff; font-size: 10px; }
        .ok { background: #1b5e20; }
        .bad { background: #b71c1c; }
        .logo { width: 36px; height: auto; vertical-align: middle; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <div class="row">
                <div class="col col-left">
                    <div class="title">
                        <img class="logo" src="{{ public_path('img/escudo.jpg') }}" alt="Escudo">
                        LICENCIA DE CONDUCIR (TAXI)
                    </div>

                    <div class="label">N° Licencia</div>
                    <div class="value">{{ $licencia->num_licencia }}</div>

                    <div style="margin-top:6px">
                        <div class="label">Chofer</div>
                        <div class="value">{{ $licencia->chofer?->nombre }} {{ $licencia->chofer?->apellido }}</div>
                        <div class="small">CI: {{ $licencia->chofer?->cedula }} {{ $licencia->chofer?->comp }}</div>
                    </div>

                    <div style="margin-top:6px">
                        <div class="label">Taxi</div>
                        <div class="value">PLACA: {{ $licencia->taxi?->placa }}</div>
                        <div class="small">{{ $licencia->taxi?->marca }} {{ $licencia->taxi?->modelo }} {{ $licencia->taxi?->linea }}</div>
                    </div>

                </div>
                <div class="col col-right">
                    <div class="label" style="text-align:right">Verificación</div>
                    <img class="qr" src="data:image/png;base64, {!! $qrcode !!}" alt="QR">
                    <div class="small" style="word-break: break-all; text-align:right">
                    </div>
                </div>
            </div>
            <div class="small" style="margin-top:6px">
                Sindicato: {{ $licencia->sindicato?->nombre ?? '—' }}
            </div>
        </div>
    </div>
</body>
</html>

