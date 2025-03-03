<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consulta Hogar</title>
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
        .session-info {
            background: #3366CC;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .search-container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        #loadingSpinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: rgba(255,255,255,0.8);
            padding: 20px;
            border-radius: 10px;
        }
        .bg-brown {
            background-color: #8B4513 !important;
            color: white !important;
        }
        .bg-white {
            background-color: #FFFFFF !important;
            color: #000000 !important;
            border: 1px solid #dee2e6 !important;
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
            <div id="loadingSpinner" class="d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">🔍 Consulta de Hogares</h4>
                        </div>
                        <div class="card-body">
                            <div id="errorMessage" class="alert alert-danger d-none"></div>

                            <!-- Pestañas de búsqueda -->
                            <ul class="nav nav-tabs" id="searchTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="folio-tab" data-bs-toggle="tab" data-bs-target="#folioTab" type="button" role="tab">
                                        <i class="bi bi-folder"></i> Búsqueda por Folio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documento-tab" data-bs-toggle="tab" data-bs-target="#documentoTab" type="button" role="tab">
                                        <i class="bi bi-person-vcard"></i> Búsqueda por Documento
                                    </button>
                                </li>
                            </ul>

                            <!-- Contenido de las pestañas -->
                            <div class="tab-content mt-3" id="searchTabsContent">
                                <!-- Búsqueda por Folio -->
                                <div class="tab-pane fade show active" id="folioTab" role="tabpanel">
                                    <form class="search-form">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="folio" placeholder="Ingrese el número de folio">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-search"></i> Buscar
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Búsqueda por Documento -->
                                <div class="tab-pane fade" id="documentoTab" role="tabpanel">
                                    <form class="search-form">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="documento" placeholder="Ingrese el número de documento">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-search"></i> Buscar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Contenedor de resultados -->
                            <div id="resultsContainer" class="mt-4">
                                @if(isset($resultados))
                                    @include('components.tabla-resultados', ['resultados' => $resultados])
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalles -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Hogar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="detailTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#hogarTab">Vista Hogar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#integrantesTab">Integrantes</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="hogarTab"></div>
                        <div class="tab-pane fade" id="integrantesTab"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.search-form').on('submit', function(e) {
                e.preventDefault();
                
                $('#loadingSpinner').removeClass('d-none');
                $('#errorMessage').addClass('d-none');
                
                var formData = $(this).serialize();
                
                $.ajax({
                    url: '{{ route('consultaHogar.process') }}',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.html) {
                            $('#resultsContainer').html(response.html);
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON?.error || 'Ocurrió un error al procesar la búsqueda';
                        $('#errorMessage').removeClass('d-none').text(errorMessage);
                    },
                    complete: function() {
                        $('#loadingSpinner').addClass('d-none');
                    }
                });
            });
        });

        function showDetails(folio) {
            $('#detailsModal').modal('show');
            $('#hogarTab').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
            $('#integrantesTab').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
            
            $.ajax({
                url: '{{ route('consultaHogar.details') }}',
                method: 'POST',
                data: { folio: folio, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.hogar) {
                        $('#hogarTab').html(response.hogar);
                    }
                    if (response.integrantes) {
                        $('#integrantesTab').html(response.integrantes);
                    }
                    // Almacenar el folio para uso posterior
                    if (response.folio) {
                        $('#integrantesTab').data('folio', response.folio);
                    }
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON?.error || 'Error al obtener los detalles';
                    $('#hogarTab, #integrantesTab').html(`
                        <div class="alert alert-danger">
                            <p>${errorMessage}</p>
                        </div>
                    `);
                }
            });
        }

        function downloadExcel(folio) {
            window.location.href = `{{ route('consultaHogar.download') }}?folio=${folio}`;
        }

        function downloadFicha(folio) {
            // Mostrar indicador de carga
            $('#loadingSpinner').removeClass('d-none');
            
            // Crear un formulario temporal
            const form = $('<form>', {
                'method': 'POST',
                'action': '{{ route("consultaHogar.downloadFicha") }}',
            });

            // Agregar el token CSRF
            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': $('meta[name="csrf-token"]').attr('content')
            }));

            // Agregar el folio
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'folio',
                'value': folio
            }));

            // Agregar el formulario al documento y enviarlo
            $('body').append(form);
            form.submit();
            form.remove();

            // Ocultar indicador de carga después de un breve delay
            setTimeout(function() {
                $('#loadingSpinner').addClass('d-none');
            }, 2000);
        }

        // Función para determinar el color del DI en la tabla resumen
        function getResumenDIColor(logros) {
            // Si hay algún blanco (5), retorna blanco
            if (logros.includes(5)) return 'white';
            // Si hay algún rojo (0), retorna rojo
            if (logros.includes(0)) return 'danger';
            // Si hay algún verde (1) y puede haber grises (2), retorna verde
            if (logros.includes(1)) return 'success';
            // Si solo hay grises (2), retorna gris
            return 'secondary';
        }

        // Función para determinar el color del DA en la tabla resumen
        function getResumenDAColor(logros) {
            // Si hay algún blanco (5), retorna blanco
            if (logros.includes(5)) return 'white';
            // Si hay algún rojo (0), retorna rojo
            if (logros.includes(0)) return 'danger';
            // Si hay algún café (4), retorna café
            if (logros.includes(4)) return 'brown';
            // Si hay algún azul (3), retorna azul
            if (logros.includes(3)) return 'primary';
            // Si hay algún verde (1), retorna verde
            if (logros.includes(1)) return 'success';
            // Si solo hay grises (2), retorna gris
            return 'secondary';
        }

        // Manejo de logros
        $(document).on('click', '.btn-ver-logros', function() {
            const idintegrante = $(this).data('idintegrante');
            const nombre = $(this).data('nombre');
            const folio = $(this).data('folio');
            
            $('#nombreIntegrante').text(nombre);
            $('#logrosContent').html('<div class="text-center"><div class="spinner-border"></div></div>');
            $('#logrosModal').modal('show');
            
            $.ajax({
                url: '{{ route("consultaHogar.logros-integrante") }}',
                method: 'POST',
                data: {
                    idintegrante: idintegrante,
                    folio: folio,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        let html = '';
                        let currentDimension = '';
                        let dimensionSummary = {};
                        
                        // Agrupamos los logros por dimensión
                        response.logros.forEach(logro => {
                            if (!dimensionSummary[logro.dimension]) {
                                dimensionSummary[logro.dimension] = {
                                    logrosDI: [], // Array para guardar todos los DI de la dimensión
                                    logrosDF: []  // Array para guardar todos los DF de la dimensión
                                };
                            }
                            dimensionSummary[logro.dimension].logrosDI.push(logro.colorlogroDI);
                            dimensionSummary[logro.dimension].logrosDF.push(logro.colorlogroPF);
                        });

                        // Crear tabla de resumen
                        html += `
                            <div class="table-responsive mb-4">
                                <h5 class="bg-primary text-white p-2 rounded">Resumen por Dimensiones</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Dimensión</th>
                                            <th>DI</th>
                                            <th>DA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        for (const [dimension, estados] of Object.entries(dimensionSummary)) {
                            const colorDI = getResumenDIColor(estados.logrosDI);
                            const colorDA = getResumenDAColor(estados.logrosDF);

                            html += `
                                <tr>
                                    <td>${dimension}</td>
                                    <td><span class="badge bg-${colorDI}" style="width: 25px; height: 25px; border-radius: 50%;">&nbsp;</span></td>
                                    <td><span class="badge bg-${colorDA}" style="width: 25px; height: 25px; border-radius: 50%;">&nbsp;</span></td>
                                </tr>
                            `;
                        }

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                        
                        // Luego construimos los detalles de los logros
                        response.logros.forEach(logro => {
                            if(logro.dimension !== currentDimension) {
                                if(currentDimension !== '') html += '</div>';
                                currentDimension = logro.dimension;
                                html += `
                                    <div class="dimension-section mb-4">
                                    <h5 class="bg-primary text-white p-2 rounded">${logro.dimension}</h5>
                                `;
                            }
                            
                            // Determinar el color para DI (Diagnóstico Inicial)
                            const colorDI = getColorClass(logro.colorlogroDI);
                            const estadoDI = getEstadoLogro(logro.colorlogroDI);
                            
                            // Determinar el color para DF (Diagnóstico Final)
                            const colorDF = getColorClass(logro.colorlogroPF);
                            const estadoDF = getEstadoLogro(logro.colorlogroPF);
                            
                            html += `
                                <div class="logro-item p-2 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <p class="mb-0">${logro.logro}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-end">
                                                <div class="me-3">
                                                    <span class="badge bg-${colorDI} me-1" style="width: 25px; height: 25px; border-radius: 50%;">&nbsp;</span>
                                                    <small>DI</small>
                                                </div>
                                                <div>
                                                    <span class="badge bg-${colorDF} me-1" style="width: 25px; height: 25px; border-radius: 50%;">&nbsp;</span>
                                                    <small>DF</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        if(currentDimension !== '') html += '</div>';
                        
                        // Agregar resumen de porcentajes si están disponibles
                        if(response.porcentajeLogrosAplican && response.porcentajeLogrosAplican.length > 0) {
                            const porcentajes = response.porcentajeLogrosAplican[0];
                            html += `
                                <div class="mt-4">
                                    <h5 class="bg-secondary text-white p-2 rounded">Resumen de Logros</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><span class="badge bg-success">Verde:</span> ${porcentajes.generalverd}%</p>
                                            <p><span class="badge bg-danger">Rojo:</span> ${porcentajes.generalrojo}%</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><span class="badge bg-secondary">Gris:</span> ${porcentajes.generalgris}%</p>
                                            <p><span class="badge bg-primary">Azul:</span> ${porcentajes.generalazul}%</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><span class="badge bg-warning">Café:</span> ${porcentajes.generalcafe}%</p>
                                            <p><strong>Porcentaje Actual:</strong> ${porcentajes.porcentajeactual}%</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                        
                        $('#logrosContent').html(html);
                    }
                },
                error: function() {
                    $('#logrosContent').html('<div class="alert alert-danger">Error al cargar los logros</div>');
                }
            });
        });

        function getColorClass(color) {
            const colors = {
                0: 'danger',     // Rojo - Falta
                1: 'success',    // Verde - Cumplido
                2: 'secondary',  // Gris - No aplica
                3: 'primary',    // Azul - Dificultad
                4: 'brown',      // Café - Desinterés
                5: 'white'       // Blanco - Recálculo
            };
            return colors[color] || 'secondary';
        }

        function getEstadoLogro(color) {
            const estados = {
                0: 'Le falta',
                1: 'Cumplido',
                2: 'No le aplica',
                3: 'Dificultad para alcanzarlo',
                4: 'Desinterés',
                5: 'Recálculo del logro'
            };
            return estados[color] || 'Sin estado';
        }
    </script>
</body>
</html>
