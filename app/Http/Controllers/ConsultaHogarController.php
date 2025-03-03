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
                $query = "
                    SELECT
                        tpi.folio,
                        tpi.idintegrante,
                        tpi.documento,
                        CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tpi.telefono,
                        tbar.barriovereda
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    INNER JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi 
                        ON tph.folio = tpi.folio 
                        AND tph.idintegrantetitular = tpi.idintegrante
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tpi.folio = ?
                ";
                $resultados = DB::select($query, [$folio]);
            } else {
                $query = "
                    SELECT
                        tpi.folio,
                        tpi.idintegrante,
                        tpi.documento,
                        CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tpi.telefono,
                        tbar.barriovereda
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tpi.documento = ?
                ";
                $resultados = DB::select($query, [$documento]);
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

            // Consulta para los integrantes
            $queryIntegrantes = "
                SELECT
                    tpi.documento,
                    tpi.idintegrante,
                    CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                    tpi.parentesco,
                    CONCAT(tpi.telefono, ' - ', tpi.celular) AS telefono
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
}
