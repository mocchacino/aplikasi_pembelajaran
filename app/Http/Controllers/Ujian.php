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
use App\M_Soal;
use App\M_Jawaban;
use App\M_Skor;

class Ujian extends Controller
{
    //MENAMPILKAN LIST SOAL
    public function listSoal(Request $request){
        $token = $request->token;
        $tokenDb = M_Peserta::where('token', $token)->count();
        if($tokenDb > 0){
            // mengambil key dari env
            $key = env('APP_KEY');
            // token di decode
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            // validasi token sudah kadaluwarsa atau belum
            if($decoded_array['extime'] > time()){
                $cal_skor = M_Skor::where('id_peserta', $decoded_array['id_peserta'])
                ->where('status','1')
                ->count();
                $id_s = '';
                // peserta sedang dalam ujian
                if($cal_skor > 0){
                    // ambil id_peserta
                    $id_s = M_Skor::where('id_peserta', $decoded_array['id_peserta'])->where('status','1')->first();
                }else{
                // peserta tidak dalam ujian
                    // membuat row baru dengan id_peserta tsb
                    M_Skor::create([
                        'id_peserta' => $decoded_array['id_peserta']
                    ]);
                    // ambil id_peserta
                    $id_s = M_Skor::where('id_peserta', $decoded_array['id_peserta'])->where('status','1')->first();
                }
                // menghitung skor menurut id_peserta
                $skor = M_Skor::where('id_peserta', $decoded_array['id_peserta'])
                ->where('status','1')
                ->first();
                
                // ambil jawaban sesuai id_peserta yang dalam ujian
                $jawaban = M_Jawaban::where('id_peserta', $decoded_array['id_peserta'])->first();

                // untuk mengetahui berapa soal yang sudah dijawab peserta
                // yang nantinya digunakan/disimpan diperulangan data[]
                $jum_jawaban = M_Jawaban::where('id_peserta', $decoded_array['id_peserta'])->where('id_skor', $skor->id_skor)->count();
               
                // jumlah soal dalam db 
                $jumlah_soal = M_Soal::count();
               
                // menampilkan 10 soal, krn 1x ujian hanya ada 10 soal
                $max_rand = $jumlah_soal - 10;
                
                // random soal
                $mulai = rand(0, $max_rand);
               
                // untuk mengambil soal dimulai dari
                $list_soal = M_Soal::skip($mulai)->take(10)->get();
               
                // menyimpan list_soal ke array
                $data = array();
                foreach($list_soal as $soal){
                    $data[] = array(
                        'id_soal' => $soal->id_soal,
                        'pertanyaan' => $soal->pertanyaan,
                        'opsi1' => $soal->opsi1,
                        'opsi2' => $soal->opsi2,
                        'opsi3' => $soal->opsi3,
                        'opsi4' => $soal->opsi4,
                        'jumlah_jawaban' => $jum_jawaban
                    );
                }
                return response()->json([
                    'status' => 'berhasil',
                    'message' => 'Data berhasil diambil',
                    'id_skor' => $id_s->id_skor,
                    'data' => $data
                ]);
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

    // SIMPAN JAWABAN
    public function jawab(Request $request){
        // membaca token
        $token = $request->token;
        // mengecek token
        $tokenDb = M_Peserta::where('token', $token)->count();
        if($tokenDb > 0){
            // mengambil key dari env
            $key = env('APP_KEY');
            // token di decode
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            // validasi token sudah kadaluwarsa atau belum
            if($decoded_array['extime'] > time()){
                // ambil soal
                $soal = M_Soal::where('id_soal', $request->id_soal)->get();
                foreach($soal as $s){
                    if($request->jawaban == $s->jawaban){
                        if(M_Jawaban::create([
                            'id_peserta' => $decoded_array['id_peserta'],
                            'id_soal' => $s->id_soal,
                            'jawaban' => $request->jawaban,
                            'id_skor' => $request->id_skor,
                            'status_jawaban' => '1'
                        ])){
                            return response()->json([
                                'status' => 'berhasil',
                                'message' => 'Data berhasil disimpan'
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'gagal',
                                'message' => 'Data gagal disimpan'
                            ]);
                        }
                    }else{
                        if(M_Jawaban::create([
                            'id_peserta' => $decoded_array['id_peserta'],
                            'id_soal' => $s->id_soal,
                            'jawaban' => $request->jawaban,
                            'id_skor' => $request->id_skor,
                            'status_jawaban' => '0'
                        ])){
                            return response()->json([
                                'status' => 'berhasil',
                                'message' => 'Data berhasil disimpan'
                            ]);
                        }else{
                            return response()->json([
                                'status' => 'gagal',
                                'message' => 'Data gagal disimpan'
                            ]);
                        }
                    }
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

    // MENGHITUNG SKOR
    public function hitungSkor(Request $request){
        // membaca token
        $token = $request->token;
        // mengecek token
        $tokenDb = M_Peserta::where('token', $token)->count();
        if($tokenDb > 0){
            // mengambil key dari env
            $key = env('APP_KEY');
            // token di decode
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            // validasi token sudah kadaluwarsa atau belum
            if($decoded_array['extime'] > time()){
                // mengambil id_peserta yang masih aktif dari tbl_skor
                $id_s = M_Skor::where('id_peserta', $decoded_array['id_peserta'])->where('status','1')->first();  
                // menghitung jawaban yang benar menurut id_skor   
                $jawaban = M_Jawaban::where('status_jawaban','1')->where('id_skor', $id_s->id_skor)->count();
                // menampilkan jumlah skor
                return response()->json([
                    'status' => 'berhasil',
                    'skor' => $jawaban
                ]);
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


    // SELESAI UJIAN
    // menonaktifkan status pada tbl_skor
    public function selesaiUjian(Request $request){
        // membaca token
        $token = $request->token;
        // mengecek token
        $tokenDb = M_Peserta::where('token', $token)->count();
        if($tokenDb > 0){
            // mengambil key dari env
            $key = env('APP_KEY');
            // token di decode
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array)$decoded;
            // validasi token sudah kadaluwarsa atau belum
            if($decoded_array['extime'] > time()){
                // mengambil id_peserta yang masih aktif dari tbl_skor
                $id_s = M_Skor::where('id_peserta', $decoded_array['id_peserta'])->where('status','1')->first();  

                // jika melakukan update
                if(M_Skor::where('id_skor', $id_s->id_skor)->update([
                    'status' => '0',
                ])){
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil diubah'
                    ]);
                }else{
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Data gagal diubah'
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
