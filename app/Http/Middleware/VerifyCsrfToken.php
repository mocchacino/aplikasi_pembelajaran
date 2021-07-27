<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // routing yang sudah dibuat
        // ditambah kesini agar bisa diakses
        // supaya data yang dikirim dari frontend
        // dapat diterima di controller
        '/tambahAdmin',
        '/loginAdmin',
        '/hapusAdmin',
        '/ubahAdmin',
        '/listAdmin',
        '/tambahKonten',
        '/ubahKonten',
        '/hapusKonten',
        '/listKonten',
        '/registrasi',
        '/loginPeserta',
        '/listKontenPeserta',
        '/listSoal',
        '/jawab',
        '/hitungSkor',
        '/selesaiUjian',
    ];
}
