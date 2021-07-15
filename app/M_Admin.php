<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Admin extends Model
{
    // env berisikan database 
    // setting struktur tabel nya di model
    // model mengambil data ke database
    protected $table = 'tbl_user';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'id_user',
        'nama',
        'email',
        'password',
        'token'
    ];

}
