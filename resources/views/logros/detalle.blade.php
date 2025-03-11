<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle de Logro - Folio: {{ $folio }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            background: #3366CC;
            color: #fff;
            padding-top: 20px;
            z-index: 1000;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #254EAB;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
        }
        .header-container {
            background-color: {{ $logro->color ?? '#3366CC' }};
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .dimension-indicator {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-left: 10px;
            vertical-align: middle;
        }
        .logro-description {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid {{ $logro->color ?? '#3366CC' }};
        }
        .status-badge {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .bg-brown {
            background-color: #8B4513;
            color: white;
        }
        .table th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .btn-volver {
            background-color: #3366CC;
            color: white;
            border: none;
        }
        .btn-volver:hover {
            background-color: #254EAB;
            color: white;
        }
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .progress {
            height: 20px;
            border-radius: 10px;
        }
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="px-3 mb-3">
            <h4>Menu</h4>
        </div>
        <a href="{{ route('search') }}">
            <i class="bi bi-search"></i> Consulta
        </a>
        <a href="{{ route('searchMef') }}">
            <i class="bi bi-people"></i> Consulta MEF
        </a>
        <a href="{{ route('consultaHogar') }}" class="active">
            <i class="bi bi-house"></i> Consulta Hogar
        </a>
        <form action="{{ route('logout') }}" method="POST" class="mt-auto">
            @csrf
            <button type="submit" class="btn btn-link text-white w-100 text-start">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </button>
        </form>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <a href="javascript:history.back()" class="btn btn-volver">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- Encabezado -->
                    <div class="header-container">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Folio: {{ $folio }}</h4>
                            <span class="badge bg-white text-dark">Dimensión: {{ $logro->dimension ?? 'No disponible' }}</span>
                        </div>
                    </div>

                    <!-- Información del logro -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Información del Logro</h5>
                        </div>
                        <div class="card-body">
                            <h4>{{ $logro->nombre_logro ?? 'Información no disponible' }}</h4>
                            <p class="text-muted">{{ $logro->descripcion ?? 'Sin descripción disponible' }}</p>
                        </div>
                    </div>

                    <!-- Resumen estadístico -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Resumen de Estado</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-2">
                                    <div class="card stats-card bg-light mb-3">
                                        <div class="card-body p-2">
                                            <h2 class="text-success mb-0">{{ $estadisticas['cumplidos'] }}</h2>
                                            <small>Cumplidos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card stats-card bg-light mb-3">
                                        <div class="card-body p-2">
                                            <h2 class="text-danger mb-0">{{ $estadisticas['pendientes'] }}</h2>
                                            <small>Pendientes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card stats-card bg-light mb-3">
                                        <div class="card-body p-2">
                                            <h2 class="text-secondary mb-0">{{ $estadisticas['no_aplica'] }}</h2>
                                            <small>No Aplica</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card stats-card bg-light mb-3">
                                        <div class="card-body p-2">
                                            <h2 class="text-primary mb-0">{{ $estadisticas['dificultad'] }}</h2>
                                            <small>Dificultad</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card stats-card bg-light mb-3">
                                        <div class="card-body p-2">
                                            <h2 style="color: #8B4513;" class="mb-0">{{ $estadisticas['desinteres'] }}</h2>
                                            <small>Desinterés</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card stats-card bg-light mb-3">
                                        <div class="card-body p-2">
                                            <h2 class="text-muted mb-0">{{ $estadisticas['sin_info'] }}</h2>
                                            <small>Sin Info</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="progress mt-3">
                                @if($estadisticas['total'] > 0)
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($estadisticas['cumplidos'] / $estadisticas['total']) * 100 }}%" aria-valuenow="{{ $estadisticas['cumplidos'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['total'] }}"></div>
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($estadisticas['pendientes'] / $estadisticas['total']) * 100 }}%" aria-valuenow="{{ $estadisticas['pendientes'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['total'] }}"></div>
                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ ($estadisticas['no_aplica'] / $estadisticas['total']) * 100 }}%" aria-valuenow="{{ $estadisticas['no_aplica'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['total'] }}"></div>
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($estadisticas['dificultad'] / $estadisticas['total']) * 100 }}%" aria-valuenow="{{ $estadisticas['dificultad'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['total'] }}"></div>
                                    <div class="progress-bar bg-brown" role="progressbar" style="width: {{ ($estadisticas['desinteres'] / $estadisticas['total']) * 100 }}%" aria-valuenow="{{ $estadisticas['desinteres'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['total'] }}"></div>
                                    <div class="progress-bar bg-white border" role="progressbar" style="width: {{ ($estadisticas['sin_info'] / $estadisticas['total']) * 100 }}%" aria-valuenow="{{ $estadisticas['sin_info'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['total'] }}"></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de resultados -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Integrantes del Hogar</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre Integrante</th>
                                            <th>Edad</th>
                                            <th>Género</th>
                                            <th>Parentesco</th>
                                            <th>Estado</th>
                                            <th>Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($resultadosLogro) > 0)
                                            @foreach($resultadosLogro as $resultado)
                                                <tr>
                                                    <td>{{ $resultado->nombre_completo ?? 'No disponible' }}</td>
                                                    <td>{{ $resultado->edad ?? 'N/A' }}</td>
                                                    <td>{{ $resultado->genero ?? 'N/A' }}</td>
                                                    <td>{{ $resultado->parentesco ?? 'N/A' }}</td>
                                                    <td>
                                                        @php
                                                            $badgeClass = 'bg-white border text-dark';
                                                            switch($resultado->estado_id) {
                                                                case 1: $badgeClass = 'bg-success'; break;
                                                                case 2: $badgeClass = 'bg-danger'; break;
                                                                case 3: $badgeClass = 'bg-secondary'; break;
                                                                case 4: $badgeClass = 'bg-primary'; break;
                                                                case 5: $badgeClass = 'bg-brown'; break;
                                                                default: $badgeClass = 'bg-white border text-dark';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ $resultado->estado }}</span>
                                                    </td>
                                                    <td>{{ $resultado->observacion ?? 'Sin observaciones' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">No hay resultados disponibles</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Leyenda de colores -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Leyenda de Estados</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3 mb-2">
                                            <span class="badge bg-success me-1" style="width: 20px; height: 20px; border-radius: 50%;">&nbsp;</span>
                                            <small>Verde - Cumplido</small>
                                        </div>
                                        <div class="me-3 mb-2">
                                            <span class="badge bg-danger me-1" style="width: 20px; height: 20px; border-radius: 50%;">&nbsp;</span>
                                            <small>Rojo - Falta</small>
                                        </div>
                                        <div class="me-3 mb-2">
                                            <span class="badge bg-secondary me-1" style="width: 20px; height: 20px; border-radius: 50%;">&nbsp;</span>
                                            <small>Gris - No aplica</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3 mb-2">
                                            <span class="badge bg-primary me-1" style="width: 20px; height: 20px; border-radius: 50%;">&nbsp;</span>
                                            <small>Azul - Dificultad</small>
                                        </div>
                                        <div class="me-3 mb-2">
                                            <span class="badge bg-brown me-1" style="width: 20px; height: 20px; border-radius: 50%;">&nbsp;</span>
                                            <small>Café - Desinterés</small>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge bg-white me-1" style="width: 20px; height: 20px; border-radius: 50%; border: 1px solid #dee2e6;">&nbsp;</span>
                                            <small>Blanco - Sin info</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
