<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchMef extends Model
{
    use HasFactory;

    protected $table = 'familiam_modulo_cif.t1_principalhogar';

    protected $fillable = [
        'folio',
        'documento',
        'nombre1',
        'nombre2',
        'apellido1',
        'apellido2',
        'direccion',
        'comuna',
        'barrio'
    ];
}
