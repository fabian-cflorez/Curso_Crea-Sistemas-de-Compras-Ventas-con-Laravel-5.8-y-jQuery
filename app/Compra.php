<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';

    protected $fillable = ['idproveedor', 'idusuario', 'tipo_identificacion', 'num_compra', 'fecha_compra', 'impuesto', 'total', 'estado'];

    public function usuario()
    {
        return $this->belongsTo('App\User');
    }

    public function proveedor()
    {
        return $this->belongsTo('App\Proveedor');
    }
}
