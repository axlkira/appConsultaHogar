<div class="container-fluid">
    <!-- Información del integrante -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Información del Integrante</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p class="mb-1"><strong>Nombre:</strong></p>
                    <p>{{ $integrante->nombre_completo }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Edad:</strong></p>
                    <p>{{ $integrante->edad }} años</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Género:</strong></p>
                    <p>{{ $integrante->genero }}</p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1"><strong>Parentesco:</strong></p>
                    <p>{{ $integrante->parentesco }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de logros -->
    <div class="row mb-4">
        @php
            $cumplidos = 0;
            $pendientes = 0;
            $noAplica = 0;
            $dificultad = 0;
            $desinteres = 0;
            $sinInfo = 0;
            $total = 0;

            foreach ($logrosPorDimension as $dimension) {
                foreach ($dimension['logros'] as $logro) {
                    $total++;
                    switch ($logro->estado_id) {
                        case 1: $cumplidos++; break;
                        case 2: $pendientes++; break;
                        case 3: $noAplica++; break;
                        case 4: $dificultad++; break;
                        case 5: $desinteres++; break;
                        default: $sinInfo++; break;
                    }
                }
            }
        @endphp

        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Resumen de Logros</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-2">
                                    <h2 class="text-success mb-0">{{ $cumplidos }}</h2>
                                    <small>Cumplidos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-2">
                                    <h2 class="text-danger mb-0">{{ $pendientes }}</h2>
                                    <small>Pendientes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-2">
                                    <h2 class="text-secondary mb-0">{{ $noAplica }}</h2>
                                    <small>No Aplica</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-2">
                                    <h2 class="text-primary mb-0">{{ $dificultad }}</h2>
                                    <small>Dificultad</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-2">
                                    <h2 style="color: #8B4513;" class="mb-0">{{ $desinteres }}</h2>
                                    <small>Desinterés</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-2">
                                    <h2 class="text-muted mb-0">{{ $sinInfo }}</h2>
                                    <small>Sin Info</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3">
                        @if($total > 0)
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($cumplidos / $total) * 100 }}%" aria-valuenow="{{ $cumplidos }}" aria-valuemin="0" aria-valuemax="{{ $total }}"></div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($pendientes / $total) * 100 }}%" aria-valuenow="{{ $pendientes }}" aria-valuemin="0" aria-valuemax="{{ $total }}"></div>
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ ($noAplica / $total) * 100 }}%" aria-valuenow="{{ $noAplica }}" aria-valuemin="0" aria-valuemax="{{ $total }}"></div>
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($dificultad / $total) * 100 }}%" aria-valuenow="{{ $dificultad }}" aria-valuemin="0" aria-valuemax="{{ $total }}"></div>
                            <div class="progress-bar bg-brown" role="progressbar" style="width: {{ ($desinteres / $total) * 100 }}%" aria-valuenow="{{ $desinteres }}" aria-valuemin="0" aria-valuemax="{{ $total }}"></div>
                            <div class="progress-bar bg-white border" role="progressbar" style="width: {{ ($sinInfo / $total) * 100 }}%" aria-valuenow="{{ $sinInfo }}" aria-valuemin="0" aria-valuemax="{{ $total }}"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logros por dimensión -->
    <div class="accordion" id="accordionDimensiones">
        @foreach($logrosPorDimension as $index => $dimension)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $index }}">
                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                        <span class="badge me-2" style="background-color: {{ $dimension['dimension']->color }}">
                            <i class="bi bi-grid"></i>
                        </span>
                        <strong>{{ $dimension['dimension']->dimension }}</strong>
                    </button>
                </h2>
                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionDimensiones">
                    <div class="accordion-body">
                        <div class="row">
                            @foreach($dimension['logros'] as $logro)
                                @php
                                    $statusClass = '';
                                    switch($logro->estado_id) {
                                        case 1: $statusClass = 'cumplido'; break;
                                        case 2: $statusClass = 'falta'; break;
                                        case 3: $statusClass = 'no-aplica'; break;
                                        case 4: $statusClass = 'dificultad'; break;
                                        case 5: $statusClass = 'desinteres'; break;
                                        default: $statusClass = ''; break;
                                    }
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="card logro-card {{ $statusClass }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="card-title logro-title mb-0">{{ $logro->nombre }}</h5>
                                                <span class="badge {{ $logro->estado_id == 1 ? 'bg-success' : ($logro->estado_id == 2 ? 'bg-danger' : ($logro->estado_id == 3 ? 'bg-secondary' : ($logro->estado_id == 4 ? 'bg-primary' : ($logro->estado_id == 5 ? 'bg-brown' : 'bg-white border text-dark')))) }}">
                                                    {{ $logro->estado }}
                                                </span>
                                            </div>
                                            <p class="card-text small">{{ $logro->descripcion }}</p>
                                            @if($logro->observacion)
                                                <div class="mt-2">
                                                    <small class="text-muted"><strong>Observación:</strong> {{ $logro->observacion }}</small>
                                                </div>
                                            @endif
                                            <div class="mt-3 text-end">
                                                <a href="{{ route('logros.detalle', ['idLogro' => $logro->idlogro, 'folio' => $folio]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-search"></i> Ver Detalle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
