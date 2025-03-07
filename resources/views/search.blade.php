<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consulta Hogar - Búsqueda</title>
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
        .search-type-selector {
            margin-bottom: 2rem;
        }
        .result-message {
            margin-top: 2rem;
            padding: 1rem;
            border-radius: 4px;
        }
        .result-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .result-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center mb-4">Consulta Hogar</h4>
        <nav>
            <a href="{{ route('home') }}"><i class="bi bi-house-door-fill me-2"></i>Inicio</a>
            <a href="{{ route('search') }}" class="active"><i class="bi bi-search me-2"></i>Búsqueda</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Session Info -->
        <div class="session-info mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-person-circle me-2"></i>
                    {{-- Usuario: {{ Auth::user()->name ?? 'Invitado' }} --}}
                </div>
                <form action="" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-white">
                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">🔍 Búsqueda de Información</h4>
                        </div>
                        <div class="card-body">
                            <!-- Pestañas de búsqueda -->
                            <ul class="nav nav-tabs" id="searchTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="folio-tab" data-bs-toggle="tab" data-bs-target="#folioTab" type="button" role="tab">
                                        <i class="bi bi-folder"></i> Búsqueda por Folio
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="document-tab" data-bs-toggle="tab" data-bs-target="#documentTab" type="button" role="tab">
                                        <i class="bi bi-person-vcard"></i> Búsqueda por Documento
                                    </button>
                                </li>
                            </ul>

                            <!-- Contenido de las pestañas -->
                            <div class="tab-content mt-3" id="searchTabsContent">
                                <!-- Búsqueda por Folio -->
                                <div class="tab-pane fade show active" id="folioTab" role="tabpanel">
                                    <form id="folioForm" method="POST" action="{{ route('search.process') }}" class="search-form">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control"
                                                   name="folio"
                                                   placeholder="Ingrese el número de folio"
                                                   value="{{ old('folio') }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-search"></i> Buscar
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Búsqueda por Documento -->
                                <div class="tab-pane fade" id="documentTab" role="tabpanel">
                                    <form id="documentForm" method="POST" action="{{ route('search.process') }}" class="search-form">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control"
                                                   name="documento"
                                                   placeholder="Ingrese el número de documento"
                                                   value="{{ old('documento') }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-search"></i> Buscar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Spinner de carga -->
                            <div id="loadingSpinner" class="text-center mt-3 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>

                            <!-- Contenedor de resultados -->
                            <div id="resultsContainer">
                                <!-- Los resultados se cargarán aquí mediante AJAX -->
                            </div>

                            <!-- Contenedor de errores -->
                            <div id="errorContainer"></div>
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
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#logrosTab">Gestiones y Logros</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="hogarTab">
                            <!-- Contenido de Vista Hogar -->
                        </div>
                        <div class="tab-pane fade" id="integrantesTab">
                            <!-- Contenido de Integrantes -->
                        </div>
                        <div class="tab-pane fade" id="logrosTab">
                            <!-- Contenido de Gestiones y Logros -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"   class="btn btn-success" id="downloadFicha">
                        <i class="bi bi-download"></i> Descargar Ficha
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configuración de AJAX para incluir el token CSRF en todas las peticiones
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Función para mostrar/ocultar el spinner de carga
            function toggleLoading(show) {
                $('#loadingSpinner').toggleClass('d-none', !show);
            }

            // Función para mostrar los resultados
            function displayResults(data) {
                const $resultsContainer = $('#resultsContainer');
                const $errorContainer = $('#errorContainer').empty();

                if (data.success) {
                    if (data.count === 0) {
                        $resultsContainer.html(`
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle"></i> No se encontraron resultados para la búsqueda.
                            </div>`);
                    } else {
                        let tableHtml = `
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Folio</th>
                                            <th>Documento</th>
                                            <th>Nombre Completo</th>
                                            <th>Dirección</th>
                                            <th>Comuna</th>
                                            <th>Barrio/Vereda</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                        $.each(data.results, function(i, result) {
                            tableHtml += `
                                <tr>
                                    <td>${result.folio}</td>
                                    <td>${result.documento}</td>
                                    <td>${result.nombrecompleto}</td>
                                    <td>${result.direccion}</td>
                                    <td>${result.comuna}</td>
                                    <td>${result.barriovereda}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary" onclick="showDetails('${result.folio}')" title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success"  onclick="downloadFicha('${result.folio}')" title="Descargar ficha">
                                                <i class="bi bi-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                        });

                        tableHtml += `
                                    </tbody>
                                </table>
                            </div>`;

                        $resultsContainer.html(tableHtml);
                    }
                } else {
                    $errorContainer.html(`
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-triangle"></i> ${data.error}
                        </div>`);
                }
            }

            // Manejador de envío de formularios
            $('.search-form').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);

                toggleLoading(true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json'
                })
                .done(function(data) {
                    displayResults(data);
                })
                .fail(function() {
                    $('#errorContainer').html(`
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-triangle"></i> Error al procesar la solicitud.
                        </div>`);
                })
                .always(function() {
                    toggleLoading(false);
                });
            });
        });

        // Función para mostrar detalles
        function showDetails(folio) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    
            // Hacer una solicitud AJAX para obtener los detalles
            $.ajax({
                url: '{{ route('search.details') }}',
                method: 'POST',
                data: {
                    folio: folio,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.results.length > 0) {
                        const result = response.results[0];
                        document.getElementById('hogarTab').innerHTML = `
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-4">Información del Hogar ${result.folio}</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6 pe-2">
                                            <p class="mb-2"><strong>Folio:</strong><br>${result.folio}</p>
                                        </div>
                                        <div class="col-md-6 ps-2 border-start">
                                            <p class="mb-2"><strong>Estado:</strong><br>Graduado</p>
                                        </div>
                                    </div>
    
                                    <div class="row mb-3">
                                        <div class="col-md-6 pe-2">
                                            <p class="mb-2"><strong>Fecha Ingreso:</strong><br>2023-01-01</p>
                                        </div>
                                        <div class="col-md-6 ps-2 border-start">
                                            <p class="mb-2"><strong>Fecha Egreso:</strong><br>2024-01-01</p>
                                        </div>
                                    </div>
    
                                    <div class="row mb-3">
                                        <div class="col-md-6 pe-2">
                                            <p class="mb-2"><strong>Dirección:</strong><br>${result.direccion}</p>
                                        </div>
                                        <div class="col-md-6 ps-2 border-start">
                                            <p class="mb-2"><strong>Teléfono:</strong>${result.telefono1}</p>
                                        </div>
                                    </div>
    
                                    <div class="row mb-3">
                                        <div class="col-md-6 pe-2">
                                            <p class="mb-2"><strong>Estrato:</strong><br>2</p>
                                        </div>
                                        <div class="col-md-6 ps-2 border-start">
                                            <p class="mb-2"><strong>Barrio/Vereda:</strong><br>${result.barriovereda}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
    
                        document.getElementById('integrantesTab').innerHTML = `
                            <div class="list-group">
                                <div class="list-group-item">
                                    <h6>Juan Pérez - Padre</h6>
                                    <p class="mb-1">Edad: 45 años</p>
                                </div>
                                <div class="list-group-item">
                                    <h6>María García - Madre</h6>
                                    <p class="mb-1">Edad: 42 años</p>
                                </div>
                            </div>
                        `;
    
                        document.getElementById('logrosTab').innerHTML = `
                            <div class="timeline">
                                <div class="alert alert-success">
                                    <h6>Enero 2024</h6>
                                    <p>Beneficio de educación aprobado</p>
                                </div>
                                <div class="alert alert-info">
                                    <h6>Diciembre 2023</h6>
                                    <p>Participación en programa comunitario</p>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('hogarTab').innerHTML = `
                            <div class="alert alert-danger">
                                <p>No se encontraron detalles para el folio ${folio}.</p>
                            </div>
                        `;
                    }
                    modal.show();
                },
                error: function() {
                    document.getElementById('hogarTab').innerHTML = `
                        <div class="alert alert-danger">
                            <p>Error al obtener los detalles. Por favor, intente nuevamente.</p>
                        </div>
                    `;
                    modal.show();
                }
            });
        }

        // Función para simular descarga de ficha
        function downloadFicha(folio) {
            alert(`Descargando ficha del hogar ${folio}...`);
        }
    </script>
</body>
</html>
