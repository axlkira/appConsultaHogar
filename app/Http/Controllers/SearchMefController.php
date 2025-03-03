<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SearchMef;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SearchMefController extends Controller
{
    public function index()
    {
        return view('searchMef');
    }

    public function process(Request $request)
    {
        try {
            $results = [];
            $folio = $request->input('folio');
            $documento = $request->input('documento');

            if ($folio) {
                $query = "
                    SELECT
                        tpi.folio,
                        tpi.documento,
                        CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tbar.barriovereda
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tpi.folio = '$folio'
                ";
                $results = DB::select($query);
            } elseif ($documento) {
                $query = "
                    SELECT
                        tpi.folio,
                        tpi.documento,
                        CONCAT_WS(' ', tpi.nombre1, tpi.nombre2, tpi.apellido1, tpi.apellido2) AS nombrecompleto,
                        tph.direccion,
                        tph.comuna,
                        tbar.barriovereda
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    WHERE tpi.documento = '$documento'
                ";
                $results = DB::select($query);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'results' => $results,
                    'count' => count($results)
                ]);
            }

            return view('searchMef', [
                'results' => collect($results)
            ]);

        } catch (\Exception $e) {
            Log::error('Error en la búsqueda: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al realizar la búsqueda. Por favor, intente nuevamente.'
                ]);
            }

            return view('searchMef', [
                'error' => 'Error al realizar la búsqueda. Por favor, intente nuevamente.'
            ]);
        }
    }
}