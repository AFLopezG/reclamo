<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamo PDF</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        .info { margin-bottom: 20px; }
        .image { text-align: center; }
        .tab1{ width:100%;}
        .tab1 th{ text-align: left; font-size: 14px}
        .tab1 td{ font-size: 14px}
        .context-title{
            text-align: left;
            font-size:1em;
            text-transform: capitalize;
            position: relative;
            border: 1px solid;
            }

            .context-title-text{
            display: inline-block;
            background-color:white;
            top:-20px;
            padding-left: 10px;
            padding-right: 10px;
            font-style: italic;
            font-weight: bold;
            text-transform:uppercase;
            }
            .firma{
                border-top: 1px solid;
                text-align: center;
                font-size: 10px;
                width: 20%;
                margin-left: 50px;
            }
    </style>
</head>
<body>
    <table style="width: 100%">
        <tr>
            <td style="width: 20%"><img src="{{ public_path('img/escudo.jpg') }}" alt="escudo" style="width: 100px; height: auto;"></td>
            <td style="width: 60%"><h1>Reclamo ATM</h1></td>
            <td class='image'><b style='font-size:10px'>No: {{$reclamo->id}}</b></b><br><img src="data:image/png;base64, {!! $qrcode !!}" style="height: 50px;width: 50px;"></td>
        </tr>
    </table>
    
    
    
    <div class="info">
        <table class='tab1'><tr><th>Fecha:</th><td>{{ $reclamo->fecha }}</td><th>Hora:</th><td>{{ $reclamo->hora }}</td></tr></table>
        <div class='context-title'>
            <div class="context-title-text">Datos Demandate</div>
            <table class="tab1"><tr><th>Cedula:</th><td>{{ $reclamo->persona->cedula  }} {{$reclamo->persona->comp}}</td><th>Nombre:</th><td>{{ $reclamo->persona->nombre }}</td><th>Teléfono:</th><td>{{ $reclamo->persona->telefono }}</td></tr>
            <tr><th>Direccion: </th><td colspan="5">{{ $reclamo->direccion }}</td></tr>
            </table>
        </div>
        <br>
        <div class='context-title'>
            <div class="context-title-text">Reclamo</div>
            <div style="padding: 5px">{{ $reclamo->descripcion }}</div>
        </div>
        </div>
        <div class='context-title'>
            <div class="context-title-text">Foto Adjunta</div>
            <div style="padding: 5px">
                @if($reclamo->imagen)
                <div class="image">
                    <img src="{{ public_path('imagenes/' . $reclamo->imagen) }}" alt="Imagen de Reclamo" style="max-width: 500px; height: 600px;">
                </div>
            @endif
            </div>
        </div>
        </div><br><br><br><br>
        <div class="firma">{{ $reclamo->persona->nombre }} <br>{{ $reclamo->persona->cedula }}</div>
    </div>


</body>
</html>
