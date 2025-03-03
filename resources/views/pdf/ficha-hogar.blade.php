<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha del Hogar - {{$hogar->folio}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3366CC;
            padding-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .section-title {
            background-color: #3366CC;
            color: white;
            padding: 5px 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    @php
        function getColorHex($color) {
            $colors = [
                0 => '#DC3545', // Rojo
                1 => '#198754', // Verde
                2 => '#6C757D', // Gris
                3 => '#0D6EFD', // Azul
                4 => '#8B4513', // Café
                5 => '#FFFFFF'  // Blanco
            ];
            return $colors[$color] ?? '#6C757D';
        }

        function getResumenDIColor($logros) {
            if (in_array(5, $logros)) return '#FFFFFF';
            if (in_array(0, $logros)) return '#DC3545';
            if (in_array(1, $logros)) return '#198754';
            return '#6C757D';
        }

        function getResumenDAColor($logros) {
            if (in_array(5, $logros)) return '#FFFFFF';
            if (in_array(0, $logros)) return '#DC3545';
            if (in_array(4, $logros)) return '#8B4513';
            if (in_array(3, $logros)) return '#0D6EFD';
            if (in_array(1, $logros)) return '#198754';
            return '#6C757D';
        }
    @endphp
    <div class="header">
        <h1>Ficha del Hogar</h1>
        <h2>Folio: {{$hogar->folio}}</h2>
    </div>

    <div class="section">
        <div class="section-title">Información del Hogar</div>
        <table>
            <tr>
                <th width="30%">Dirección:</th>
                <td>{{$hogar->direccion}}</td>
            </tr>
            <tr>
                <th>Comuna:</th>
                <td>{{$hogar->comuna}}</td>
            </tr>
            <tr>
                <th>Barrio/Vereda:</th>
                <td>{{$hogar->barriovereda}}</td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td>{{$hogar->telefono}}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Integrantes del Hogar</div>
        <table>
            <thead>
                <tr>
                    <th width="20%">Documento</th>
                    <th width="50%">Nombre Completo</th>
                    <th width="30%">Parentesco</th>
                </tr>
            </thead>
            <tbody>
                @foreach($integrantes as $integrante)
                <tr>
                    <td>{{$integrante->documento}}</td>
                    <td>{{$integrante->nombrecompleto}}</td>
                    <td>{{$integrante->parentesco}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Líneas y Estaciones</div>
        <table>
            <thead>
                <tr>
                    <th width="25%">Línea</th>
                    <th width="25%">Estación</th>
                    <th width="25%">Estado</th>
                    <th width="25%">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lineasEstaciones as $linea)
                <tr>
                    <td>{{$linea->desclinea}}</td>
                    <td>{{$linea->descripcion}}</td>
                    <td>{{$linea->nombreestado}}</td>
                    <td>{{$linea->fecharegistro}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(isset($dimensionSummary) && count($dimensionSummary) > 0)
        <div style="page-break-before: always;">
            <h4 style="background-color: #0d6efd; color: white; padding: 10px; border-radius: 5px;">Resumen de Logros por Dimensiones</h4>
            <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #dee2e6; padding: 8px;">Dimensión</th>
                        <th style="border: 1px solid #dee2e6; padding: 8px;">DI</th>
                        <th style="border: 1px solid #dee2e6; padding: 8px;">DA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dimensionSummary as $dimension => $estados)
                        <tr>
                            <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $dimension }}</td>
                            <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ getResumenDIColor($estados['logrosDI']) }}; border: 1px solid #dee2e6; margin: 0 auto;"></div>
                            </td>
                            <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ getResumenDAColor($estados['logrosDF']) }}; border: 1px solid #dee2e6; margin: 0 auto;"></div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4 style="background-color: #0d6efd; color: white; padding: 10px; border-radius: 5px; margin-top: 20px;">Detalle de Logros por Dimensiones</h4>
            @foreach($dimensionSummary as $dimension => $data)
                <div style="margin-bottom: 20px;">
                    <h5 style="background-color: #0d6efd; color: white; padding: 5px 10px; border-radius: 3px;">{{ $dimension }}</h5>
                    @foreach($data['logros'] as $logro)
                        <div style="padding: 5px 0; border-bottom: 1px solid #dee2e6;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 70%;">{{ $logro->logro }}</td>
                                    <td style="width: 30%; text-align: right;">
                                        <div style="display: inline-block; margin-right: 10px;">
                                            <div style="width: 15px; height: 15px; border-radius: 50%; background-color: {{ getColorHex($logro->colorlogroDI) }}; border: 1px solid #dee2e6; display: inline-block; vertical-align: middle;"></div>
                                            <small style="margin-left: 2px;">DI</small>
                                        </div>
                                        <div style="display: inline-block;">
                                            <div style="width: 15px; height: 15px; border-radius: 50%; background-color: {{ getColorHex($logro->colorlogroPF) }}; border: 1px solid #dee2e6; display: inline-block; vertical-align: middle;"></div>
                                            <small style="margin-left: 2px;">DF</small>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        Documento generado el: {{ date('Y-m-d H:i:s') }}
    </div>
</body>
</html>
