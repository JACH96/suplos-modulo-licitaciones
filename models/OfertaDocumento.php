<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaDocumento extends Model
{
    protected $table = 'ofertas_documentos';

    protected $fillable = [
        'licitacion_id',
        'titulo',
        'descripcion',
        'archivo'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = NULL;
    public $timestamps = true;
}