<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'codigo_segmento',
        'segmento',
        'codigo_familia',
        'familia',
        'codigo_clase',
        'clase',
        'codigo_producto',
        'producto'
    ];

    public $timestamps = false;
}