<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Integrante;

class ConsultaHogar extends Model
{
    protected $connection = 'mysql';
    protected $table = 'familiam_modulo_cif.t1_principalhogar';
    protected $primaryKey = 'folio';
    public $timestamps = false;

    public function integrantes()
    {
        return $this->hasMany(Integrante::class, 'folio', 'folio');
    }

    public static function buscarPorFolioODocumento($folio = '', $documento = '')
    {
        try {
            if (empty($folio) && empty($documento)) {
                return collect();
            }

            $query = "";
            if (!empty($folio)) {
                $query = "
                    SELECT
                        tph.folio,
                        tph.direccion,
                        tph.comuna,
                        tbar.barriovereda as barrio,
                        tdep.departamento,
                        tmun.municipio,
                        CASE WHEN tph.estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    LEFT JOIN t_municipiosc1p3 AS tmun ON tmun.codigo = tph.municipio
                    LEFT JOIN t_departamentosc1p2 AS tdep ON tdep.codigo = tph.departamento
                    WHERE tph.folio = '$folio'
                ";
            } elseif (!empty($documento)) {
                $query = "
                    SELECT
                        tph.folio,
                        tph.direccion,
                        tph.comuna,
                        tbar.barriovereda as barrio,
                        tdep.departamento,
                        tmun.municipio,
                        CASE WHEN tph.estado = 1 THEN 'Activo' ELSE 'Inactivo' END as estado
                    FROM familiam_modulo_cif.t1_principalhogar AS tph
                    LEFT JOIN familiam_modulo_cif.t1_principalintegrantes AS tpi ON tph.folio = tpi.folio
                    LEFT JOIN t_barriosc1p5 AS tbar ON tbar.codigo = tph.barrio
                    LEFT JOIN t_municipiosc1p3 AS tmun ON tmun.codigo = tph.municipio
                    LEFT JOIN t_departamentosc1p2 AS tdep ON tdep.codigo = tph.departamento
                    WHERE tpi.documento = '$documento'
                ";
            }

            return collect(DB::select($query));

        } catch (\Exception $e) {
            return collect();
        }
    }
}
