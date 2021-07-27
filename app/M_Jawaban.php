<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Jawaban extends Model
{
    // env berisikan database 
    // setting struktur tabel nya di model
    // model mengambil data ke database
    protected $table = 'tbl_jawaban';
    protected $primaryKey = 'id_jawaban';
    protected $fillable = [
        'id_jawaban',
        'id_peserta',
        'id_soal',
        'id_skor',
        'jawaban',
        'status_jawaban'

    ];
}
