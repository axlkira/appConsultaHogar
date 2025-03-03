@if(isset($resultados) && count($resultados) > 0)
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Folio</th>
                    <th>Documento</th>
                    <th>Nombre Completo</th>
                    <th>Dirección</th>
                    <th>Comuna</th>
                    <th>Barrio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados as $resultado)
                    <tr>
                        <td>{{ $resultado->folio }}</td>
                        <td>{{ $resultado->documento }}</td>
                        <td>{{ $resultado->nombrecompleto }}</td>
                        <td>{{ $resultado->direccion }}</td>
                        <td>{{ $resultado->comuna }}</td>
                        <td>{{ $resultado->barriovereda }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal" onclick="showDetails('{{ $resultado->folio }}')">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="downloadFicha('{{ $resultado->folio }}')">
                                <i class="bi bi-download"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info mt-3">
        No se encontraron resultados.
    </div>
@endif
