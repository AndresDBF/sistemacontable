<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobantePago extends Model
{
    use HasFactory;
    protected $primaryKey = 'idpag';
    protected $fillable = [
        'idorpa',	
        'idasi',	
        'numconfirm',
        'moneda',	
        'montolocal',
        'montomoneda',
        'tasa_cambio',
        'cantidad_escr'
    ];
}
