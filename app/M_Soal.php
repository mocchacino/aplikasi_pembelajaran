<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Soal extends Model
{
    // env berisikan database 
    // setting struktur tabel nya di model
    // model mengambil data ke database
    protected $table = 'tbl_soal';
    protected $primaryKey = 'id_soal';
    protected $fillable = [
        'id_soal',
        'pertanyaan',
        'opsi1',
        'opsi2',
        'opsi3',
        'opsi4',
        'jawaban',

    ];
}
