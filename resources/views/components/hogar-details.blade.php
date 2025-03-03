@if(isset($hogar))
    <div class="table-responsive">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th width="30%">Folio</th>
                    <td>{{ $hogar->folio }}</td>
                </tr>
                <tr>
                    <th>Documento</th>
                    <td>{{ $hogar->documento }}</td>
                </tr>
                <tr>
                    <th>Parentesco</th>
                    <td>{{ $hogar->parentesco }}</td>
                </tr>
                <tr>
                    <th>Nombre Completo</th>
                    <td>{{ $hogar->nombrecompleto }}</td>
                </tr>
                <tr>
                    <th>Dirección</th>
                    <td>{{ $hogar->direccion }}</td>
                </tr>
                <tr>
                    <th>Telefono</th>
                    <td>{{ $hogar->telefono }}</td>
                </tr>
                <tr>
                    <th>Comuna</th>
                    <td>{{ $hogar->comuna }}</td>
                </tr>
                <tr>
                    <th>Barrio/Vereda</th>
                    <td>{{ $hogar->barriovereda }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @php
        \Log::info('Líneas y estaciones en la vista: ' . (isset($lineasEstaciones) ? count($lineasEstaciones) : 'no definido'));
    @endphp

    @if(isset($lineasEstaciones) && count($lineasEstaciones) > 0)
        <div class="mt-4">
            <h5 class="border-bottom pb-2">Líneas y Estaciones del Hogar</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Línea</th>
                            <th>Estación</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Fecha Servidor</th>
                            <th>Doc. Gestor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lineasEstaciones as $linea)
                            <tr>
                                <td>{{ $linea->desclinea }}</td>
                                <td>{{ $linea->descripcion }}</td>
                                <td>
                                    @if($linea->nombreestado == 'EFECTIVA')
                                        <span class="badge bg-success">EFECTIVA</span>
                                    @elseif($linea->nombreestado)
                                        <span class="badge bg-warning">{{ $linea->nombreestado }}</span>
                                    @else
                                        <span class="badge bg-secondary">Sin Estado</span>
                                    @endif
                                </td>
                                <td>{{ $linea->fecharegistro }}</td>
                                <td>{{ $linea->fecharegistroservidor }}</td>
                                <td>{{ $linea->doccogestor }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info mt-4">
            <p class="mb-0">No se encontraron líneas y estaciones para este hogar.</p>
        </div>
    @endif
@else
    <div class="alert alert-info">
        No se encontraron datos del hogar.
    </div>
@endif
