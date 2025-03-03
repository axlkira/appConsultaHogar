<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integrante extends Model
{
    protected $table = 'familiam_modulo_cif.t1_principalintegrantes';
    protected $primaryKey = ['folio', 'idintegrante'];
    public $incrementing = false;
    public $timestamps = false;

    public function hogar()
    {
        return $this->belongsTo(ConsultaHogar::class, 'folio', 'folio');
    }
}
