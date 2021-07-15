<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class M_Materi extends Model
{
    // env berisikan database 
    // setting struktur tabel nya di model
    // model mengambil data ke database
    protected $table = 'tbl_konten';
    protected $primaryKey = 'id_konten';
    protected $fillable = [
        'id_konten',
        'judul',
        'keterangan',
        'link_thumbnail',
        'link_video',
        'status',
        'view',

    ];
}
