@if(isset($logroResultado) && count($logroResultado) > 0)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Detalle del Logro</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Información del Logro</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light">Dimensión:</th>
                            <td>{{ $logroResultado[0]->dimension ?? 'No disponible' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Logro:</th>
                            <td>{{ $logroResultado[0]->logro ?? 'No disponible' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Descripción:</th>
                            <td>{{ $logroResultado[0]->descripcion ?? 'No disponible' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Información del Integrante Seleccionado</h6>
                    @if(isset($datosIntegrante) && count($datosIntegrante) > 0)
                        <table class="table table-bordered">
                            <tr>
                                <th class="bg-light">Nombre:</th>
                                <td>{{ $datosIntegrante[0]->nombrecompleto ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Documento:</th>
                                <td>{{ $datosIntegrante[0]->documento ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Folio:</th>
                                <td>{{ $folio }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Teléfono:</th>
                                <td>{{ $datosIntegrante[0]->telefono ?? 'No disponible' }}</td>
                            </tr>
                        </table>
                    @else
                        <div class="alert alert-info">No hay información disponible del integrante.</div>
                    @endif
                </div>
            </div>

            <!-- Tabla de integrantes a los que aplica el logro -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Integrantes a los que aplica este logro</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre del integrante</th>
                                    <th>Edad</th>
                                    <th>Género</th>
                                    <th>Documento</th>
                                    <th>Estado Inicial</th>
                                    <th>Estado Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logroResultado as $integrante)
                                    <tr>
                                        <td>{{ $integrante->nombreintegrante ?? 'No disponible' }}</td>
                                        <td>{{ $integrante->edad ?? 'No disponible' }}</td>
                                        <td>{{ $integrante->sexo ?? 'No disponible' }}</td>
                                        <td>
                                            @if(isset($integrante->registrocivil))
                                                {{ $integrante->registrocivil }}
                                            @elseif(isset($integrante->tarjetaidentidad))
                                                {{ $integrante->tarjetaidentidad }}
                                            @elseif(isset($integrante->cedulaciudadania))
                                                {{ $integrante->cedulaciudadania }}
                                            @else
                                                No disponible
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $colorClasses[$integrante->colorlogroDI ?? 2] ?? 'badge-gris' }}">
                                                &nbsp;
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $colorClasses[$integrante->colorlogroPF ?? 2] ?? 'badge-gris' }}">
                                                &nbsp;
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Observaciones</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones del logro</label>
                        <textarea class="form-control" id="observaciones" rows="3" readonly>{{ $logroResultado[0]->observaciones ?? 'No hay observaciones registradas.' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="button" class="btn btn-secondary" id="btnVolverLogros">Volver a Logros</button>
        </div>
    </div>
@else
    <div class="alert alert-warning">
        No se encontró información para el logro seleccionado.
    </div>
@endif

<style>
.badge-rojo { 
    background-color: #DF0101; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-verde { 
    background-color: #30E24E; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-gris { 
    background-color: #D1DFD3; 
    color: black; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-azul { 
    background-color: #428BCA; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-cafe { 
    background-color: #8B4513; 
    color: white; 
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.badge-blanco { 
    background-color: #FFFFFF; 
    color: black; 
    border: 1px solid #ccc;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
