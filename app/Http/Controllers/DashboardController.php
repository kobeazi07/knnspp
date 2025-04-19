<?php

namespace App\Http\Controllers;
use App\Models\Pendidikan;
use App\Models\Siswa;
use Illuminate\Http\Request;

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

        return [
            'n_salary_ayah' => $n_s_ayah,
            'n_salary_ibu' => $n_s_ibu,
            'n_pendidikan_ayah' => $n_pnd_ayah,
            'n_pendidikan_ibu' => $n_pnd_ibu
        ];
        dd($n_pnd_ibu);

        // $n_s_ayah
        
    }

    private function NormalisasiSayah($value)
    {
        $min = Siswa::min('salary_ayah'); 
        $max = Siswa::max('salary_ayah');
        $n_sayah = $value - $min / $max - $min;
        return ($n_sayah);
    }
    private function NormalisasiSibu($value)
    {
        $min = Siswa::min('salary_ibu');    
        $max = Siswa::max('salary_ibu');
        $n_sibu = $value - $min / $max - $min;
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
        $n_pndayah = $value - $pnd_min / $pnd_max - $pnd_min;
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

        $n_pndibu = $value - $pnd_min / $pnd_max - $pnd_min;
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
