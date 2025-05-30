<?php

namespace App\Http\Controllers;

use App\Models\ConsultaHogar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultaHogarController extends Controller
{
    public function index()
    {
        return view('consultaHogar');
    }

    public function process(Request $request)
    {
        try {
            $folio = trim($request->input('folio'));
            $documento = trim($request->input('documento'));

            if (empty($folio) && empty($documento)) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Por favor ingrese un folio o documento para buscar.'
                    ], 400);
                }
                return back()->with('error', 'Por favor ingrese un folio o documento para buscar.');
            }

            $resultados = [];
            
            if (!empty($folio)) {
                // Consulta original para CIF
                $query = "
                    SELECT
                        tpi.folio,
                        tpi.idintegrante,
                        tpi.documento,
                        CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tpi.telefono,
                        tbar.barriovereda,
                        'CIF' AS metodologia
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    INNER JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi 
                        ON tph.folio = tpi.folio 
                        AND tph.idintegrantetitular = tpi.idintegrante
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tpi.folio = ?
                ";
                $resultadosCif = DB::select($query, [$folio]);
                
                // Consulta para MEF - Solo el representante (representante = 1)
                $queryMef = "
                    SELECT
                        tis.folio,
                        tis.idintegrante,
                        tis.documento,
                        CONCAT_WS(' ', tis.nombre1, tis.nombre2, tis.apellido1, tis.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tis.telefono,
                        tbar.barriovereda,
                        'MEF' AS metodologia
                    FROM dbmetodologia_servidor.t1_integranteshogar_s AS tis
                    LEFT JOIN familiam_modulo_cif.t1_principalhogar AS tph ON tis.folio = tph.folio
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tis.folio = ? AND tis.representante = '1'
                ";
                $resultadosMef = DB::select($queryMef, [$folio]);
                
                // Combinar resultados
                $resultados = array_merge($resultadosCif, $resultadosMef);
            } else {
                // Consulta original para CIF
                $query = "
                    SELECT
                        tpi.folio,
                        tpi.idintegrante,
                        tpi.documento,
                        CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tpi.telefono,
                        tbar.barriovereda,
                        'CIF' AS metodologia
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tpi.documento = ?
                ";
                $resultadosCif = DB::select($query, [$documento]);
                
                // Consulta para MEF
                $queryMef = "
                    SELECT
                        tis.folio,
                        tis.idintegrante,
                        tis.documento,
                        CONCAT_WS(' ', tis.nombre1, tis.nombre2, tis.apellido1, tis.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tis.telefono,
                        tbar.barriovereda,
                        'MEF' AS metodologia
                    FROM dbmetodologia_servidor.t1_integranteshogar_s AS tis
                    LEFT JOIN familiam_modulo_cif.t1_principalhogar AS tph ON tis.folio = tph.folio
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tis.documento = ?
                ";
                $resultadosMef = DB::select($queryMef, [$documento]);
                
                // Combinar resultados
                $resultados = array_merge($resultadosCif, $resultadosMef);
            }

            if ($request->ajax()) {
                $html = view('components.tabla-resultados', [
                    'resultados' => collect($resultados),
                    'folio' => $folio
                ])->render();
                return response()->json(['html' => $html]);
            }

            return view('consultaHogar', [
                'resultados' => collect($resultados),
                'folio' => $folio
            ]);

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getDetails(Request $request)
    {
        try {
            $folio = $request->input('folio');
            
            if (empty($folio)) {
                return response()->json([
                    'error' => 'Folio no proporcionado'
                ], 400);
            }

            // Consulta para el hogar
            $queryHogar = "
                SELECT 
                    tph.folio,
                    tpi.documento,
                    CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                    tph.direccion,
                    tph.comuna,
                    tbar.barriovereda,
                    tpi.parentesco,
                    tpi.telefono
                FROM familiam_modulo_cif.t1_principalhogar AS tph
                LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                WHERE tph.folio = ?
                LIMIT 1
            ";
            
            $hogar = DB::selectOne($queryHogar, [$folio]);

            if (!$hogar) {
                return response()->json([
                    'error' => 'Hogar no encontrado'
                ], 404);
            }

            // Consulta para los integrantes de CIF
            $queryIntegrantes = "
                SELECT
                    tpi.documento,
                    tpi.idintegrante,
                    CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                    tpi.parentesco,
                    CONCAT(tpi.telefono, ' - ', tpi.celular) AS telefono,
                    'CIF' AS metodologia
                FROM familiam_modulo_cif.t1_principalintegrantes tpi
                WHERE tpi.folio = ?
            ";
            $integrantesCif = DB::select($queryIntegrantes, [$folio]);
            
            // Consulta para los integrantes de MEF - Solo el representante (representante = 1)
            $queryIntegrantesMef = "
                SELECT
                    tis.documento,
                    tis.idintegrante,
                    CONCAT_WS(' ', tis.nombre1, tis.nombre2, tis.apellido1, tis.apellido2) AS nombrecompleto,
                    tis.parentesco,
                    CONCAT(tis.telefono, ' - ', tis.celular) AS telefono,
                    'MEF' AS metodologia
                FROM dbmetodologia_servidor.t1_integranteshogar_s tis
                WHERE tis.folio = ? AND tis.representante = '1'
            ";
            $integrantesMef = DB::select($queryIntegrantesMef, [$folio]);
            
            // Combinar integrantes de ambas fuentes
            $integrantes = array_merge($integrantesCif, $integrantesMef);

            // Consulta para líneas y estaciones
            $queryLineasEstaciones = "
                SELECT 
                    t.folio,
                    t.estado,
                    DATE_FORMAT(t.fecharegistro, '%Y-%m-%d') AS fecharegistro,
                    t.idestacion,
                    s.desclinea,
                    s.descripcion,
                    n.nombreestado,
                    t.doccogestor,
                    DATE_FORMAT(t.fecharegistroservidor, '%Y-%m-%d') AS fecharegistroservidor
                FROM familiam_bdprotocoloservidor.t_historicoestacionestadoservidor t
                LEFT JOIN familiam_bdprotocoloservidor.t_protocoloestaciones s ON s.idestacion = t.idestacion
                LEFT JOIN familiam_bdprotocoloservidor.t2_estado2016 n ON n.idestado = t.estado
                WHERE t.folio = ?
                UNION
                SELECT 
                    t.folio,
                    t.estado,
                    DATE_FORMAT(t.fecharegistro, '%Y-%m-%d') AS fecharegistro,
                    t.idestacion,
                    s.desclinea,
                    s.descripcion,
                    n.nombreestado,
                    t.doccogestor,
                    DATE_FORMAT(t.fecharegistro, '%Y-%m-%d') AS fecharegistroservidor
                FROM familiam_bdprotocoloservidor.t_historicoestacionestadoservidoreditar_llbf t
                LEFT JOIN familiam_bdprotocoloservidor.t_protocoloestaciones s ON s.idestacion = t.idestacion
                LEFT JOIN familiam_bdprotocoloservidor.t2_estado2016 n ON n.idestado = t.estado
                WHERE t.folio = ?
                ORDER BY fecharegistro DESC
            ";
            
            Log::info('Consultando líneas y estaciones para folio: ' . $folio);
            $lineasEstaciones = DB::select($queryLineasEstaciones, [$folio, $folio]);
            Log::info('Resultados encontrados: ' . count($lineasEstaciones));

            // Renderizar las vistas
            $hogarView = view('components.hogar-details', [
                'hogar' => $hogar,
                'lineasEstaciones' => $lineasEstaciones,
                'folio' => $folio
            ])->render();

            $integrantesView = view('components.integrantes-list', [
                'integrantes' => $integrantes,
                'folio' => $folio
            ])->render();

            return response()->json([
                'hogar' => $hogarView,
                'integrantes' => $integrantesView,
                'folio' => $folio
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getDetails: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener los detalles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download(Request $request)
    {
        try {
            $folio = $request->query('folio');
            
            if (empty($folio)) {
                return back()->with('error', 'Folio no proporcionado');
            }

            $query = "
                SELECT
                    tpi.folio,
                    tpi.documento,
                    CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                    tph.direccion,
                    tph.comuna,
                    tpi.telefono,
                    tpi.celular,
                    tbar.barriovereda
                FROM familiam_modulo_cif.t1_principalhogar AS tph
                LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                WHERE tpi.folio = ?
                LIMIT 1
            ";
            $hogar = DB::selectOne($query, [$folio]);
            
            if (!$hogar) {
                return back()->with('error', 'Hogar no encontrado');
            }

            return back()->with('error', 'Funcionalidad de descarga en desarrollo');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getDimensiones()
    {
        try {
            $dimensiones = DB::select("
                SELECT DISTINCT iddimension, dimension 
                FROM familiam_bdprotocoloservidor.t4_dimensionlogros 
                ORDER BY iddimension
            ");
            
            return response()->json([
                'success' => true,
                'dimensiones' => $dimensiones
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener dimensiones: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLogrosIntegrante(Request $request)
    {
        try {
            $idintegrante = $request->input('idintegrante');
            $folio = $request->input('folio');
            
            if (empty($idintegrante) || empty($folio)) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID de integrante y folio son requeridos'
                ], 400);
            }

            // Obtener lista de logros usando sp4listarlogros
            $logros = DB::select('call familiam_bdprotocoloservidor.sp4listarlogros()');

            // Para cada logro, obtener su estado usando sp4totallogros
            foreach ($logros as $logro) {
                $colorResult = DB::select('call familiam_bdprotocoloservidor.sp4totallogros(?, ?)', [$folio, $logro->idlogro]);
                if (!empty($colorResult)) {
                    $logro->colorlogroDI = $colorResult[0]->logro1 ?? 2;  // Default to gray if null
                    $logro->colorlogroPF = $colorResult[0]->logro1PF ?? 2;  // Default to gray if null
                } else {
                    $logro->colorlogroDI = 2;  // Gray
                    $logro->colorlogroPF = 2;  // Gray
                }
            }

            // Obtener el porcentaje de logros que aplican
            $porcentajeLogrosAplican = DB::select("
                SELECT * from familiam_bdprotocoloservidor.t_porcentajelogrosaplican_servidor 
                WHERE folio = ?", [$folio]
            );

            // Obtener el total de porcentaje de logros
            $totalPorcentajeLogros = DB::select("
                SELECT * from familiam_bdprotocoloservidor.t_totalporcentajelogros_servidor 
                WHERE folio = ?", [$folio]
            );

            return response()->json([
                'success' => true,
                'logros' => $logros,
                'porcentajeLogrosAplican' => $porcentajeLogrosAplican,
                'totalPorcentajeLogros' => $totalPorcentajeLogros
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFicha(Request $request)
    {
        try {
            $folio = $request->input('folio');
            
            if (empty($folio)) {
                return response()->json([
                    'error' => 'Folio no proporcionado'
                ], 400);
            }

            // Consulta para el hogar
            $queryHogar = "
                SELECT 
                    tph.folio,
                    tpi.documento,
                    CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                    tph.direccion,
                    tph.comuna,
                    tbar.barriovereda,
                    tpi.parentesco,
                    tpi.telefono
                FROM familiam_modulo_cif.t1_principalhogar AS tph
                LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                WHERE tph.folio = ?
                LIMIT 1
            ";
            
            $hogar = DB::selectOne($queryHogar, [$folio]);

            if (!$hogar) {
                return response()->json([
                    'error' => 'Hogar no encontrado'
                ], 404);
            }

            // Consulta para los integrantes
            $queryIntegrantes = "
                SELECT
                    tpi.documento,
                    tpi.idintegrante,
                    CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                    tpi.parentesco
                FROM familiam_modulo_cif.t1_principalintegrantes tpi
                WHERE tpi.folio = ?
            ";
            $integrantes = DB::select($queryIntegrantes, [$folio]);

            // Consulta para líneas y estaciones
            $queryLineasEstaciones = "
                SELECT 
                    t.folio,
                    t.estado,
                    DATE_FORMAT(t.fecharegistro, '%Y-%m-%d') AS fecharegistro,
                    t.idestacion,
                    s.desclinea,
                    s.descripcion,
                    n.nombreestado,
                    t.doccogestor,
                    DATE_FORMAT(t.fecharegistroservidor, '%Y-%m-%d') AS fecharegistroservidor
                FROM familiam_bdprotocoloservidor.t_historicoestacionestadoservidor t
                LEFT JOIN familiam_bdprotocoloservidor.t_protocoloestaciones s ON s.idestacion = t.idestacion
                LEFT JOIN familiam_bdprotocoloservidor.t2_estado2016 n ON n.idestado = t.estado
                WHERE t.folio = ?
                UNION
                SELECT 
                    t.folio,
                    t.estado,
                    DATE_FORMAT(t.fecharegistro, '%Y-%m-%d') AS fecharegistro,
                    t.idestacion,
                    s.desclinea,
                    s.descripcion,
                    n.nombreestado,
                    t.doccogestor,
                    DATE_FORMAT(t.fecharegistro, '%Y-%m-%d') AS fecharegistroservidor
                FROM familiam_bdprotocoloservidor.t_historicoestacionestadoservidoreditar_llbf t
                LEFT JOIN familiam_bdprotocoloservidor.t_protocoloestaciones s ON s.idestacion = t.idestacion
                LEFT JOIN familiam_bdprotocoloservidor.t2_estado2016 n ON n.idestado = t.estado
                WHERE t.folio = ?
                ORDER BY fecharegistro DESC
            ";
            
            $lineasEstaciones = DB::select($queryLineasEstaciones, [$folio, $folio]);

            // Obtener lista de logros usando sp4listarlogros
            $logros = DB::select('call familiam_bdprotocoloservidor.sp4listarlogros()');

            // Para cada logro, obtener su estado usando sp4totallogros
            foreach ($logros as $logro) {
                $colorResult = DB::select('call familiam_bdprotocoloservidor.sp4totallogros(?, ?)', [$folio, $logro->idlogro]);
                if (!empty($colorResult)) {
                    $logro->colorlogroDI = $colorResult[0]->logro1 ?? 2;  // Default to gray if null
                    $logro->colorlogroPF = $colorResult[0]->logro1PF ?? 2;  // Default to gray if null
                } else {
                    $logro->colorlogroDI = 2;  // Gray
                    $logro->colorlogroPF = 2;  // Gray
                }
            }

            // Agrupar logros por dimensión
            $dimensionSummary = [];
            foreach ($logros as $logro) {
                if (!isset($dimensionSummary[$logro->dimension])) {
                    $dimensionSummary[$logro->dimension] = [
                        'logrosDI' => [],
                        'logrosDF' => [],
                        'logros' => []
                    ];
                }
                $dimensionSummary[$logro->dimension]['logrosDI'][] = $logro->colorlogroDI;
                $dimensionSummary[$logro->dimension]['logrosDF'][] = $logro->colorlogroPF;
                $dimensionSummary[$logro->dimension]['logros'][] = $logro;
            }

            $pdf = PDF::loadView('pdf.ficha-hogar', [
                'hogar' => $hogar,
                'integrantes' => $integrantes,
                'lineasEstaciones' => $lineasEstaciones,
                'dimensionSummary' => $dimensionSummary
            ]);

            return $pdf->download('ficha-hogar-' . $folio . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error en downloadFicha: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al generar la ficha: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetalleLogro(Request $request)
    {
        try {
            $idlogro = $request->input('idlogro');
            $iddimension = $request->input('iddimension');
            $folio = $request->input('folio');
            $idintegrante = $request->input('idintegrante');
            
            if (empty($idlogro) || empty($folio)) {
                return response()->json([
                    'success' => false,
                    'error' => 'ID de logro y folio son requeridos'
                ], 400);
            }

            // Obtener resultado detallado del logro para todos los integrantes
            $logroResultado = DB::select('call familiam_bdprotocoloservidor.sp4logroresultado(?, ?)', [$folio, $idlogro]);
            
            // Obtener datos del integrante actual (para mostrar en el encabezado)
            $datosIntegrante = DB::select('call familiam_modulo_cif.spdatosintegrante(?, ?)', [$folio, $idintegrante]);
            
            // Mapeo de colores a nombres de clases
            $colorClasses = [
                0 => 'badge-rojo',    // Rojo - No cumple
                1 => 'badge-verde',   // Verde - Cumple
                2 => 'badge-gris',    // Gris - No aplica
                3 => 'badge-azul',    // Azul - En proceso
                4 => 'badge-cafe',    // Café - Pendiente
                5 => 'badge-blanco'   // Blanco - No evaluado
            ];
            
            // Mapeo de colores a textos descriptivos
            $colorTexts = [
                0 => 'No cumple',
                1 => 'Cumple',
                2 => 'No aplica',
                3 => 'En proceso',
                4 => 'Pendiente',
                5 => 'No evaluado'
            ];
            
            // Renderizar la vista con los datos
            $html = view('components.detalle-logro', [
                'logroResultado' => $logroResultado,
                'datosIntegrante' => $datosIntegrante,
                'colorClasses' => $colorClasses,
                'colorTexts' => $colorTexts,
                'folio' => $folio,
                'idlogro' => $idlogro,
                'idintegrante' => $idintegrante
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener detalle del logro: ' . $e->getMessage()
            ], 500);
        }
    }
}
