<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PemakaianHarian extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'bahan_baku_id',
        'jumlah_terpakai',
        'tanggal',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
