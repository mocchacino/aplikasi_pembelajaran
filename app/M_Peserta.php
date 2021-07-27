<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Peserta extends Model
{
    // env berisikan database 
    // setting struktur tabel nya di model
    // model mengambil data ke database
    protected $table = 'tbl_peserta';
    protected $primaryKey = 'id_peserta';
    protected $fillable = [
        'id_peserta',
        'nama',
        'email',
        'password',
        'token',
        'status'
    ];
}
