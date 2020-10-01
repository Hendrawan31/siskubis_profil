<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfilUser extends Model
{
	protected $table ='profil_user';
   	protected $fillable = [
       'user_id', 'nama', 'jenkel', 'kontak', 'alamat', 'nik', 'foto', 'deskripsi', 'status'
    ];
}
