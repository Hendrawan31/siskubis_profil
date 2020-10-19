<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    public function tenant()
    {
    	return $this->belongsTo('App\tenant','tenant_id','id');
    }

    public function priority()
    {
    	return $this->belongsTo('App\Priority');
    }

    public function produk_image()
    {
    	return $this->belongsTo('App\ProdukImage','id','produk_id');
    }
}
