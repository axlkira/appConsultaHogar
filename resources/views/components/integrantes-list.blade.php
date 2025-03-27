@if(isset($integrantes) && count($integrantes) > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Parentesco</th>
                    <th>Numero Contacto</th>
                    <th>Metodología</th>
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
                        <span class="badge {{ $integrante->metodologia == 'MEF' ? 'bg-success' : 'bg-primary' }}">
                            {{ $integrante->metodologia }}
                        </span>
                    </td>
                    <td>
                        @if($integrante->metodologia == 'CIF')
                            <!-- Botón para CIF (funcionalidad actual) -->
                            <button class="btn btn-sm btn-primary btn-ver-logros" 
                                    data-idintegrante="{{ $integrante->idintegrante }}"
                                    data-nombre="{{ $integrante->nombrecompleto }}"
                                    data-folio="{{ $folio }}">
                                <i class="fas fa-list-check"></i> Ver Logros
                            </button>
                        @else
                            <!-- Botón para MEF (sin funcionalidad por ahora) -->
                            <button class="btn btn-sm btn-success btn-ver-logros-mef">
                                <i class="fas fa-list-check"></i> Ver Logros MEF
                            </button>
                        @endif
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
    
    <!-- Script para botones MEF (sin funcionalidad por ahora) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mefButtons = document.querySelectorAll('.btn-ver-logros-mef');
            mefButtons.forEach(button => {
                button.addEventListener('click', function() {
                    alert('Funcionalidad de logros MEF en desarrollo');
                });
            });
        });
    </script>
@else
    <div class="alert alert-info">
        No se encontraron integrantes registrados.
    </div>
@endif
