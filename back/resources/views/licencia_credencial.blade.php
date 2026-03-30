<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credencial - {{ $licencia->num_licencia }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
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
        .col-right { width: 35%; text-align: center; }
        .title { font-weight: bold; font-size: 13px; margin-bottom: 4px; }
        .label { color: #555; font-size: 10px; }
        .value { font-weight: bold; }
        .qr { width: 78px; height: 78px; margin-top: 6px; }
        .photo { width: 78px; height: 78px; object-fit: cover; border: 1px solid #222; border-radius: 6px; }
        .photo-placeholder { width: 78px; height: 78px; border: 1px solid #222; border-radius: 6px; display: table; }
        .photo-placeholder span { display: table-cell; vertical-align: middle; text-align: center; font-size: 9px; color: #666; }
        .small { font-size: 9px; color: #666; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; color: #fff; font-size: 10px; }
        .ok { background: #1b5e20; }
        .bad { background: #b71c1c; }
        .logo { width: 36px; height: auto; vertical-align: middle; }
    </style>
</head>
<body>
    @php
        $fotoPath = null;
        if ($licencia->chofer && !empty($licencia->chofer->foto)) {
            $candidate = public_path('storage/' . ltrim((string) $licencia->chofer->foto, '/'));
            if (is_file($candidate)) {
                $fotoPath = $candidate;
            }
        }
    @endphp
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
                    <div class="label">Taxista</div>
                    <div class="value">{{ $licencia->chofer?->nombre }} {{ $licencia->chofer?->apellido }}</div>
                    <div class="small">CI: {{ $licencia->chofer?->cedula }} {{ $licencia->chofer?->comp }}</div>
                </div>

                <div style="margin-top:6px">
                    <div class="label">Taxi</div>
                    <div class="value">PLACA: {{ $licencia->taxi?->placa }}</div>
                    <div class="small">{{ $licencia->taxi?->marca }} {{ $licencia->taxi?->modelo }} </div>
                </div>
            </div>
            <div class="col col-right">
                @if($fotoPath)
                    <img class="photo" src="{{ $fotoPath }}" alt="Foto">
                @else
                    <div class="photo-placeholder"><span>SIN FOTO</span></div>
                @endif
                <img class="qr" src="data:image/png;base64, {!! $qrcode !!}" alt="QR">
            </div>
        </div>
        <div class="small" style="margin-top:6px">
            Sindicato: {{ $licencia->sindicato?->nombre ?? '-' }}
        </div>
    </div>
</body>
</html>
