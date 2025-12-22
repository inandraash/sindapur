<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_bakus';
    
    protected $fillable = [
        'nama_bahan', 
        'stok_terkini', 
        'satuan',
        'stok_maksimum',
    ];

    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }

    public function transaksiStoks()
    {
        return $this->hasMany(TransaksiStok::class);
    }

    public function pemakaianHarian()
    {
        return $this->hasMany(PemakaianHarian::class);
    }
}
