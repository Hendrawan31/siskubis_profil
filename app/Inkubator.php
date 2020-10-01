<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Berita;

class Inkubator extends Model
{
    protected $table = 'inkubator';

    public function berita()
    {
    	return $this->belongsTo(Berita::class);

    public function disposisi()
    {
        return $this->belongsTo('App\Disposisi');
    }
}
