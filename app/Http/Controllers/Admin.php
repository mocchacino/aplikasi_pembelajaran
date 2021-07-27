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
use App\M_Admin;

class Admin extends Controller
{
    //tambah Admin
    // request untuk menerima inputan/request dari form 
    public function tambahAdmin(Request $request){
        // memvalidasi semua request
        $validator = Validator::make($request -> all(), [
            // apa saja yang mau divalidasi
            'nama' => 'required',
            // email harus unik di dalam tabel user
            'email' => 'required | unique:tbl_user',
            'password' => 'required',
            'token' => 'required'
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
        
        // mengambil token dari request
        $token = $request->token;
        // menegecek token tsb ada di database atau tidak
        // dengan menghitung token yang sama
        $tokenDb = M_admin::where('token', $token)->count();
        if($tokenDb > 0){
            // mengambil key dari env
            $key = env('APP_KEY');
            // token di decode
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            // validasi token sudah kadaluwarsa atau belum
            if($decoded_array['extime'] > time()){
                if(M_Admin::create(
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
            }else{
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token Kadaluwarsa'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token tidak valid'
            ]);
        }
    }



    public function loginAdmin(Request $request){
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
        $cek = M_Admin::where('email', $request->email)->count();
        // mengambil data email sama seperti dinput
        $admin = M_Admin::where('email', $request->email)->get();

        if($cek > 0){
            foreach($admin as $adm){
                // pengecekan apakah password yang dimasukkan sama dengan yang disimpan
                if($request->password == decrypt($adm->password)){
                    $key = env('APP_KEY');
                    $data = array(
                        // membuat waktu
                        // waktu 120 menit
                        'extime' => time()+(60*120),
                        'id_user' => $adm->id_user
                    );
                    // membuat token
                    $jwt = JWT::encode($data, $key);
                    // mengubah token yang sudah ada dengan token yang baru
                    M_Admin::where('id_user', $adm->id_user)->update(
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


    public function hapusAdmin(Request $request){
        $validator = Validator::make($request -> all(), [
            'id_user' => 'required',
            'token' => 'required'
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;

        $tokenDb = M_admin::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            
            if($decoded_array['extime'] > time()){
                // menghapus user menurut id_user yang dipilih 
                if(M_Admin::where('id_user', $request->id_user)->delete()){
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil dihapus'
                    ]);

                }else{
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Data tidak berhasil dihapus'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token Kadaluwarsa'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token tidak valid'
            ]);
        }
    }


    public function listAdmin(Request $request){
        $validator = Validator::make($request -> all(), [
            'token' => 'required'
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;

        $tokenDb = M_admin::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            
            if($decoded_array['extime'] > time()){
                // mengambil data user
                $admin = M_Admin::get();
                $data = array();

                // mengisikan array di variabel data
                foreach($admin as $adm){
                    $data[] = array(
                        'nama' => $adm->nama,
                        'email' => $adm->email,
                        'id_user' => $adm->id_user
                    );
                }
                
                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $data
                ]);
            }else{
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token kadaluwarsa'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token tidak valid'
            ]);
        }
    }

    // Ubah Admin
    public function ubahAdmin(Request $request){
        $validator = Validator::make($request -> all(), [
            'nama' => 'required',
            'email' => 'required | unique:tbl_user,email,'.$request->id_user.',id_user',
            'password' => 'required',
            'id_user' => 'required',
            
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        $token = $request->token;

        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            
            if($decoded_array['extime'] > time()){
                if(M_Admin::where('id_user', $request->id_user)->update(
                    [
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'password' => encrypt($request->password)
                    ]
                )){
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil diubah'
                    ]);

                }else{
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Data tidak berhasil diubah'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token Kadaluwarsa'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token tidak valid'
            ]);
        }
    }
}
