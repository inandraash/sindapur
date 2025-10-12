<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiStok extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id', 
        'jumlah_masuk', 
        'tanggal_masuk',
        'user_id'
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
