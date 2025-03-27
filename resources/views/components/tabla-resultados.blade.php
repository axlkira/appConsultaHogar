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
                    <th>Metodología</th>
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
                            <span class="badge {{ $resultado->metodologia == 'MEF' ? 'bg-success' : 'bg-primary' }}">
                                {{ $resultado->metodologia }}
                            </span>
                        </td>
                        <td>
                            @if($resultado->metodologia == 'CIF')
                                <!-- Botones para CIF (funcionalidad actual) -->
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailsModal" onclick="showDetails('{{ $resultado->folio }}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="downloadFicha('{{ $resultado->folio }}')">
                                    <i class="bi bi-download"></i>
                                </button>
                            @else
                                <!-- Botones para MEF (sin funcionalidad por ahora) -->
                                <button type="button" class="btn btn-info btn-sm btn-mef" title="Ver detalles MEF">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-sm btn-mef" title="Descargar ficha MEF">
                                    <i class="bi bi-download"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Script para botones MEF (sin funcionalidad por ahora) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mefButtons = document.querySelectorAll('.btn-mef');
            mefButtons.forEach(button => {
                button.addEventListener('click', function() {
                    alert('Funcionalidad para MEF en desarrollo');
                });
            });
        });
    </script>
@else
    <div class="alert alert-info mt-3">
        No se encontraron resultados.
    </div>
@endif
