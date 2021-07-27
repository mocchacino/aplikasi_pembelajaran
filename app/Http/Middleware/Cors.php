<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    //data yang di post dari forntend 
        // tidak langsung masuk ke controller
        // harus melewati middleware dulu untuk keamanan laravel 
    public function handle($request, Closure $next)
    {
        return $next($request)
        // setting akses controll di header
        // semua IP dari mana saja dapat mengakses
        ->header('Access-Control-Allow-Origin', '*')
        // methodnya apa
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        // headernya origin
        ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Request-With')
        ->header('Access-Control-Allow-Credentials', 'true');
    }
}
