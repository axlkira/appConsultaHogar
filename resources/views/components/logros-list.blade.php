<div class="card">
    <div class="card-body">
        <h6 class="card-subtitle mb-3 text-muted">Gestiones y Logros</h6>
        @if(isset($folio))
            <div class="alert alert-info">
                <p class="mb-0">La información de logros para el folio {{ $folio }} estará disponible próximamente.</p>
            </div>
        @else
            <div class="alert alert-warning">
                <p class="mb-0">No se pudo obtener la información de logros.</p>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="text-center">Información no disponible</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
