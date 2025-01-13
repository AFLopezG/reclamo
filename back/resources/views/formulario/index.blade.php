
<div class="container">
    <h1>Reclamos de Población</h1>
    
    <!-- Formulario de Filtro de Fechas -->
    <form action="{{ route('formulario.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="from_date">Desde</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="to_date">Hasta</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de Reclamos -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Dirección</th>
                <th>Descripción</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formularios as $reclamo)
                <tr>
                    <td>{{ $reclamo->fecha }}</td>
                    <td>{{ $reclamo->hora }}</td>
                    <td>{{ $reclamo->direccion }}</td>
                    <td>{{ $reclamo->descripcion }}</td>
                    <td>{{ $reclamo->persona->nombre }}</td>
                    <td>{{ $reclamo->persona->telefono }}</td>
                    <td>
                        <!-- Ver Imagen Modal -->
                        @if($reclamo->imagen)
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#imageModal{{ $reclamo->id }}">
                                Ver Imagen
                            </button>

                            <!-- Modal de Imagen -->
                            <div class="modal fade" id="imageModal{{ $reclamo->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $reclamo->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel{{ $reclamo->id }}">Imagen de Reclamo</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="{{ asset('storage/imagenes/' . $reclamo->imagen) }}" alt="Imagen de Reclamo" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Generar PDF -->
                        <a href="{{ route('formulario.pdf', $reclamo->id) }}" class="btn btn-danger btn-sm">Generar PDF</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>