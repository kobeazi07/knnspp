<?php

namespace App\Http\Controllers;
use App\Models\Pendidikan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\R_Pembayaran;

class DashboardController extends Controller
{
    public function dashboard(){
        return view('pages.dashboard');
    }

    public function halaman_daftar(){
        $pendidikan = Pendidikan::get();
        $pendidikans = Pendidikan::get();
        return view('pages.daftarsiswa',compact('pendidikan','pendidikans'));
    }

    public function normalisasi_data(Request $request){
        // dd($request->all());
        $salary_ayah = $request->salary_ayah;
        $salary_ibu = $request->salary_ibu;
        $pnd_ayah = $request ->pend_ayah;
        
        $data_pnd_ayah = Pendidikan::find($pnd_ayah);
        $b_pnd_ayah = $data_pnd_ayah ? $data_pnd_ayah->transformasi : null;
        
        $pnd_ibu = $request->pend_ibu;
        $data_pnd_ibu = Pendidikan::find($pnd_ibu);
        $b_pnd_ibu = $data_pnd_ibu ? $data_pnd_ibu->transformasi : null;
        // dd($b_pnd_ibu);
        // $b_pnd_ayah = $pnd_ayah->r_pnd_ayah->transformasi;
        $n_s_ayah = $this->NormalisasiSayah($salary_ayah);
        $n_s_ibu = $this->NormalisasiSibu($salary_ibu);
        $n_pnd_ayah = $this->NormalisasiPayah($b_pnd_ayah);
        $n_pnd_ibu = $this->NormalisasiPibu($b_pnd_ibu);
        
        $siswall = Siswa::all();
        $siswapp = Siswa::with('r_pnd_ayah')->get();
        foreach ($siswapp as $data) {
            if ($data->r_pnd_ayah) {
                $pnd_ayah = $data->r_pnd_ayah->transformasi;
                $pnd_ibu = $data->r_pnd_ibu->transformasi;
                // $spnd_ayah = $this->PAminMaxNormalization($pnd_ayah);
                // $spnd_ibu = $this->PIminMaxNormalization($pnd_ibu);
            } else {
                $pnd_ayah = '-'; // atau bisa juga null atau "Tidak diketahui"
            }
            $hpnd_ayah[] = $pnd_ayah;
            $hpnd_ibu[] = $pnd_ibu;   
            $nama_siswa[] =  $data->nama;
            $id_siswa[] =  $data->id;      
            
        }
        $minayah = min($hpnd_ayah);
        $maxayah = max($hpnd_ayah);
        $minibu = min($hpnd_ibu);
        $maxibu = max($hpnd_ibu);
        // dd($maxibu);
        
        foreach($hpnd_ayah as $aa){
            $spnd_ayah = $this->PAnormalization($aa, $minayah, $maxayah);
            $hspnd_ayah[] = $spnd_ayah;
            
        }
                foreach($hpnd_ibu as $ii){
                    $spnd_ibu = $this->PInormalization($ii,$minibu, $maxibu);
                    $hspnd_ibu[] = $spnd_ibu;
                }
                //    dd($hspnd_ibu);
                // dd($siswall);
                foreach ($siswall as $s) {
                    $s->salary_ayah = $this->minMaxNormalization($s->salary_ayah);
                $s->salary_ibu = $this->minMaxNormalization($s->salary_ibu);
            

                $hasila[] =   $s->salary_ayah;
                $hasili[] =   $s->salary_ibu;
            }
        //    return response()->json([
        //     'n_salary_ayah' => $n_s_ayah,
        //     'n_salary_ibu' => $n_s_ibu,
        //     'n_pendidikan_ayah' => $n_pnd_ayah,
        //     'n_pendidikan_ibu' => $n_pnd_ibu,
        //     'n_all_salary_ayah' => $hasila,
        //     'n_all_salary_ibu' => $hasili,
        //     'n_all_pend_ayah' =>  $hspnd_ayah,
        //     'n_all_pend_ibu' =>  $hspnd_ibu,
        //     new PostinganResource($postingan)
        // ]);
        // return api json yang proper
        $data = [
            'n_salary_ayah' => $n_s_ayah,
            'n_salary_ibu' => $n_s_ibu,
            'n_pendidikan_ayah' => $n_pnd_ayah,
            'n_pendidikan_ibu' => $n_pnd_ibu,
            'n_all_salary_ayah' => $hasila,
            'n_all_salary_ibu' => $hasili,
            'n_all_pend_ayah' => $hspnd_ayah,
            'n_all_pend_ibu' => $hspnd_ibu,
            'nama_siswa' =>  $nama_siswa,
            'id_siswa' =>  $id_siswa,
        ];
        // dd($data);
        // Storage::put('python/input_data.json', json_encode($data));
        Storage::put('python/input_data.json', json_encode($data, JSON_PRETTY_PRINT));
        // proses setelah pythion
        $pythonScript = base_path('storage/app/python/feature_selection.py');
        $pythonScript = str_replace('\\', '/', $pythonScript);  // ganti backslash ke slash biar aman
        
        $output = [];
        $return_var = 0;
        
        // Jalankan Python, tangkap output dan kode return
        exec("python \"$pythonScript\" 2>&1", $output, $return_var);
        // dd($output);

        if ($return_var !== 0) {
            // Kalau error, tampilkan pesan dan output
            dd('Error saat menjalankan Python:', $output, $return_var);
        }

        // Gabungkan output baris menjadi satu string JSON
        $jsonOutput = implode("", $output);

        // Decode JSON ke array PHP
        $nearestNeighbors = json_decode($jsonOutput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('Error decode JSON:', json_last_error_msg(), $jsonOutput);
        }
        $ids = array_column($nearestNeighbors, 'id');
        $pembayaran = R_Pembayaran::whereIn('siswa_id', $ids)->get();
        // dd($pembayaran);
        // Hitung jumlah status
        $tepatWaktu = $pembayaran->where('status_pembayaran', 'tepat waktu')->count();
        $terlambat  = $pembayaran->count() - $tepatWaktu; // sisanya dianggap terlambat
        // dd($tepatWaktu);
        // Tentukan hasil
        $statusPotensi = $tepatWaktu > $terlambat ? 'Tidak berpotensi terlambat' : 'Berpotensi terlambat';
        // dd($statusPotensi);
        return response()->json([
        'potensi' => $statusPotensi,    
        ]);
        //    try {
        //         $response = Http::post('http://127.0.0.1:8000/process', $data);
        //         $python_result = $response->json();
        //     } catch (\Exception $e) {
        //         return response()->json(['error' => 'Gagal koneksi ke Python API', 'message' => $e->getMessage()]);
        //     }
        //     return response()->json([
        //         'php_data' => $data,
        //         'python_result' => $python_result
        //     ]);
        // dd($data);
        
        // $response = Http::get('http://127.0.0.1:5000/process', $data);
        
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Data berhasil dinormalisasi',
        //     'data' => [
        //         'n_salary_ayah' => $n_s_ayah,
        //         'n_salary_ibu' => $n_s_ibu,
        //         'n_pendidikan_ayah' => $n_pnd_ayah,
        //         'n_pendidikan_ibu' => $n_pnd_ibu,
        //         'n_all_salary_ayah' => $hasila,
        //         'n_all_salary_ibu' => $hasili,
        //         'n_all_pend_ayah' => $hspnd_ayah,
        //         'n_all_pend_ibu' => $hspnd_ibu
        //         ]
        //     ], 200);
        // Kirim ke Python service (ubah URL sesuai Python Flask / FastAPI kamu)
            // $path = Storage::path('python\diagnose.py');
        // dd($n_pnd_ibu);
        // $n_s_ayah
        
    }
    
    private function minMaxNormalization($value)
    {
        // dd($value);
        $max = Siswa::max('salary_ayah'); 
        $min = Siswa::min('salary_ayah');
        if ($max - $min == 0) {
            return 0; 
        }
        // Normalisasi dengan Min-Max
        return ($value - $min) / ($max - $min);
    }
    private function PAnormalization($value, $minayah, $maxayah)
    {
        // dd($minayah);
        $min_ayah = $minayah;
        $max_ayah = $maxayah;
        $n_p_ayah = ($value - $min_ayah) / ($max_ayah - $min_ayah);
        // dd(  $n_p_ayah);
        // Normalisasi dengan Min-Max
        return ($n_p_ayah);
    }
    private function PInormalization($value, $minibu, $maxibu)
    {
        // dd($minayah);
        $min_ibu = $minibu;
        $max_ibu = $maxibu;
        $n_p_ibu = ($value - $min_ibu) / ($max_ibu - $min_ibu);
        // Normalisasi dengan Min-Max
        return ($n_p_ibu);
    }
    private function NormalisasiSayah($value)
    {
        $min = Siswa::min('salary_ayah'); 
        $max = Siswa::max('salary_ayah');
        $n_sayah = ($value - $min) / ($max - $min);
        return ($n_sayah);
    }
    private function NormalisasiSibu($value)
    {
        $min = Siswa::min('salary_ibu');    
        $max = Siswa::max('salary_ibu');
        $n_sibu = ($value - $min) / ($max - $min);
        // dd($n_sibu);
        return ($n_sibu);
    }
    private function NormalisasiPayah($value)
    {   
        $siswa = Siswa::with('r_pnd_ayah')->get();
        // $siswa = Siswa::get();
      
        foreach ($siswa as $data) {
            if ($data->r_pnd_ayah) {
                $pnd_ayah = $data->r_pnd_ayah->transformasi;
            } else {
                $pnd_ayah = '-'; // atau bisa juga null atau "Tidak diketahui"
            }
            // echo "ID Siswa: {$data->id} - Transformasi Ayah: {$pnd_ayah}<br>";
            // $hasil[] = [
            //     $pnd_ayah
            //     // 'id' => $data->id,
            // ];
            $hasil[] = $pnd_ayah;
        }
    
        $pnd_min = min($hasil);
        $pnd_max = max($hasil);
        $n_pndayah = ($value - $pnd_min )/ ($pnd_max - $pnd_min);
        // dd($n_pndayah);
        return ($n_pndayah);
    }
    private function NormalisasiPibu($value)
    {
        $siswa = Siswa::with('r_pnd_ibu')->get();
        foreach ($siswa as $data) {
            if ($data->r_pnd_ibu) {
                $pnd_ibu = $data->r_pnd_ibu->transformasi;
            } else {
                $pnd_ibu = '-'; // atau bisa juga null atau "Tidak diketahui"
            }
            $hasil[] = $pnd_ibu;
        }
    
        $pnd_min = min($hasil);
        $pnd_max = max($hasil);

        $n_pndibu = ($value - $pnd_min) / ($pnd_max - $pnd_min);
        // dd($n_pndibu);
        return ($n_pndibu);
    }
    // private function NormalisasiSibu($value)
    // {
    //     $min = Siswa::min('salary_ibu');    
    //     $max = Siswa::max('salary_ibu');
    //     $n_sibu = $value - $min / $max - $min;
    //     // dd($n_sibu);
    //     return ($n_sibu);
    // }

}
