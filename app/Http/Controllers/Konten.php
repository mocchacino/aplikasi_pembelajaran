<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

// untuk menerima inputan form/respon form
use Illuminate\Http\Response;
// untuk memvalidate inputan form
use Illuminate\Support\Facades\Validator;

use Illuminate\Contracts\Encryption\DecryptionException;

use App\M_Admin;
use App\M_Materi;
use App\M_Peserta;

class Konten extends Controller
{
    //tambah Konten
    // request : menerima inputan/request dari form
    public function tambahKonten(Request $request){
        // memvalidasi input
        $validator = Validator::make($request -> all(), [
            'keterangan' => 'required',
            'link_video' => 'required',
            'link_thumbnail' => 'required',
            'judul' => 'required | unique:tbl_konten',
            
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }

        // mengambil token dari request
        $token = $request->token;
        // mengecek apakah token ada di database atau tidak
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb > 0){
            // mengambil.menyimpan key dari env
            $key = env('APP_KEY');
            // mendecode token
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            // mengecek apakan token sudah kadaluwarsa atau belum
            if($decoded_array['extime'] > time()){
                // insert/create data baru ke table
                if(M_Materi::create(
                    [
                        'judul' => $request->judul, 
                        'keterangan' => $request->keterangan,
                        'link_thumbnail' => $request->link_thumbnail, 
                        'link_video' => $request->link_video, 
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

    // Ubah Konten
    public function ubahKonten(Request $request){
        // memvalidasi request dari form 
        $validator = Validator::make($request -> all(), [
            'keterangan' => 'required',
            'link_video' => 'required',
            'link_thumbnail' => 'required',
            // judul harus unik di tabel konten, di kolom judul,
            //  except request id_konten, primary key
            'judul' => 'required | unique:tbl_konten,judul,'.$request->id_konten.',id_konten',
            'id_konten' => 'required'
            
        ]);

        if($validator -> fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
        // menyimpan/mengambil token
        $token = $request->token;
        // mengecek token ada atau tidak
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            
            if($decoded_array['extime'] > time()){
                if(M_Materi::where('id_konten', $request->id_konten)->update(
                    [
                        'judul' => $request->judul, 
                        'keterangan' => $request->keterangan,
                        'link_thumbnail' => $request->link_thumbnail, 
                        'link_video' => $request->link_video, 
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


    //Hapus Konten
    public function hapusKonten(Request $request){
        $validator = Validator::make($request -> all(), [
            'id_konten' => 'required',
            'token' => 'required'
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
                if(M_Materi::where('id_konten', $request->id_konten)->delete()){
                    
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


    //List Konten
    public function listKonten(Request $request){
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

        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            
            if($decoded_array['extime'] > time()){
                $konten = M_Materi::get();
                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $konten
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

    //List Konten Peserta
    public function listKontenPeserta(Request $request){
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

        $tokenDb = M_Peserta::where('token', $token)->count();
        if($tokenDb > 0){
            $key = env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            
            if($decoded_array['extime'] > time()){
                $konten = M_Materi::get();
                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'data' => $konten
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
}
