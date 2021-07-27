<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illmuniate\Support\Facades\Session;

// import JWT-PHP
use \Firebase\JWT\JWT;

// untuk menerima inputan form/respon form
use Illuminate\Http\Response;
// untuk memvalidate inputan form
use Illuminate\Support\Facades\Validator;

use Illuminate\Contracts\Encryption\DecryptionException;

// import model
use App\M_Peserta;

class Peserta extends Controller
{
    //tambah Peserta
    // request untuk menerima inputan/request dari form 
    public function registrasiPeserta(Request $request){
        // memvalidasi semua request
        $validator = Validator::make($request -> all(), [
            // apa saja yang mau divalidasi
            'nama' => 'required',
            // email harus unik di dalam tabel peserta
            'email' => 'required | unique:tbl_peserta',
            'password' => 'required |confirmed',
            'password_confirmation' => 'required'
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
        
        if(M_Peserta::create(
            [
                'nama' => $request->nama, 
                'email' => $request->email,
                'password' => encrypt($request->password)
            ]
        )){
            return response()->json([
                'status' => 'berhasil',
                'message' => 'Data berhasil disimpan'
            ]);

        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Data tidak berhasil disimpan'
            ]);
        }
    }

    // LOGIN PESERTA
    public function loginPeserta(Request $request){
        $validator = Validator::make($request -> all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
        // mengecek apakah email yang diinputkan ada
        $cek = M_Peserta::where('email', $request->email)->count();
        // mengambil data email sama seperti dinput
        $peserta = M_Peserta::where('email', $request->email)->get();

        if($cek > 0){
            foreach($peserta as $psrt){
                // pengecekan apakah password yang dimasukkan sama dengan yang disimpan
                if($request->password == decrypt($psrt->password)){
                    $key = env('APP_KEY');
                    $data = array(
                        // membuat waktu
                        // waktu 120 menit
                        'extime' => time()+(60*120),
                        'id_peserta' => $psrt->id_peserta
                    );
                    // membuat token
                    $jwt = JWT::encode($data, $key);
                    // mengubah token yang sudah ada dengan token yang baru
                    M_Peserta::where('id_peserta', $psrt->id_peserta)->update(
                        [
                            'token' => $jwt
                        ]
                        );
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Berhasil Login',
                        'token' => $jwt
                    ]);
                }else{
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Password salah'
                    ]);
                }
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Email tidak terdaftar'
            ]);
        }
    }
}
