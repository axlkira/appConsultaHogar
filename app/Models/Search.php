<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'search_type',
        'search_query',
        'result_found',
        'result_data'
    ];

    protected $casts = [
        'result_data' => 'array',
        'result_found' => 'boolean'
    ];

    /**
     * Obtiene el usuario que realizó la búsqueda
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
