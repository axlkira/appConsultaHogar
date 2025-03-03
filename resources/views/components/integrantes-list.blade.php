@if(isset($integrantes) && count($integrantes) > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Parentesco</th>
                    <th>Numero Contacto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($integrantes as $integrante)
                <tr>
                    <td>{{ $integrante->documento }}</td>
                    <td>{{ $integrante->nombrecompleto }}</td>
                    <td>{{ $integrante->parentesco }}</td>
                    <td>{{ $integrante->telefono }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-ver-logros" 
                                data-idintegrante="{{ $integrante->idintegrante }}"
                                data-nombre="{{ $integrante->nombrecompleto }}"
                                data-folio="{{ $folio }}">
                            <i class="fas fa-list-check"></i> Ver Logros
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para logros -->
    <div class="modal fade" id="logrosModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Logros de: <span id="nombreIntegrante"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="logrosContent" class="container-fluid"></div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        No se encontraron integrantes registrados.
    </div>
@endif
