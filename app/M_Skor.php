<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Skor extends Model
{
    // env berisikan database 
    // setting struktur tabel nya di model
    // model mengambil data ke database
    protected $table = 'tbl_skor';
    protected $primaryKey = 'id_skor';
    protected $fillable = [
        'id_skor',
        'id_peserta',
        'skor',
        'status',

    ];
}
