<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    
    protected $table ='siswa';
    protected $guarded =[];

    public function r_pnd_ayah()
    {
        return $this->belongsTo(Pendidikan::class, 'pnd_ayah');
    }

    public function r_pnd_ibu()
    {
        return $this->belongsTo(Pendidikan::class, 'pnd_ibu');
    } 
}
