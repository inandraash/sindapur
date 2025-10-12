<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_menu', 
        'harga',
    ];
    
    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }
    
    public function transaksiPenjualans()
    {
        return $this->hasMany(TransaksiPenjualan::class);
    }
}