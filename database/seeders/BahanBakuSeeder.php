<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BahanBaku;

class BahanBakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bahanBakus = [
            ['nama_bahan' => 'Daging Sapi', 'satuan' => 'kg', 'stok_terkini' => 20],
            ['nama_bahan' => 'Daging Ayam', 'satuan' => 'kg', 'stok_terkini' => 25],
            ['nama_bahan' => 'Telur Ayam', 'satuan' => 'butir', 'stok_terkini' => 100],

            ['nama_bahan' => 'Beras', 'satuan' => 'kg', 'stok_terkini' => 50],
            ['nama_bahan' => 'Kangkung', 'satuan' => 'ikat', 'stok_terkini' => 30],
            ['nama_bahan' => 'Tauge', 'satuan' => 'kg', 'stok_terkini' => 5],
            ['nama_bahan' => 'Kacang Panjang', 'satuan' => 'ikat', 'stok_terkini' => 20],
            ['nama_bahan' => 'Buncis', 'satuan' => 'kg', 'stok_terkini' => 10],
            ['nama_bahan' => 'Wortel', 'satuan' => 'kg', 'stok_terkini' => 10],

            ['nama_bahan' => 'Bumbu Pecel (Siap Pakai)', 'satuan' => 'kg', 'stok_terkini' => 15],
            ['nama_bahan' => 'Orek Tempe (Matang)', 'satuan' => 'kg', 'stok_terkini' => 5],
            ['nama_bahan' => 'Sambal Terasi', 'satuan' => 'kg', 'stok_terkini' => 5],
            ['nama_bahan' => 'Bumbu Dasar Soto', 'satuan' => 'kg', 'stok_terkini' => 10],
            ['nama_bahan' => 'Bumbu Dasar Asem-Asem', 'satuan' => 'kg', 'stok_terkini' => 10],
            ['nama_bahan' => 'Minyak Goreng', 'satuan' => 'liter', 'stok_terkini' => 20],
            ['nama_bahan' => 'Bawang Goreng', 'satuan' => 'kg', 'stok_terkini' => 2],
            ['nama_bahan' => 'Kerupuk Udang', 'satuan' => 'pcs', 'stok_terkini' => 200],
            ['nama_bahan' => 'Peyek Kacang', 'satuan' => 'pcs', 'stok_terkini' => 100],
        ];

        foreach ($bahanBakus as $bahan) {
            if (!isset($bahan['stok_maksimum'])) {
                $base = floatval($bahan['stok_terkini'] ?? 0);
                $name = strtolower($bahan['nama_bahan']);
                $satuan = strtolower($bahan['satuan']);

                if (strpos($name, 'daging sapi') !== false) {
                    $bahan['stok_maksimum'] = max(50, $base * 3);
                } elseif (strpos($name, 'daging ayam') !== false) {
                    $bahan['stok_maksimum'] = max(60, $base * 3);
                } elseif (strpos($name, 'telur') !== false) {
                    $bahan['stok_maksimum'] = max(500, $base * 5);
                } elseif (strpos($name, 'beras') !== false) {
                    $bahan['stok_maksimum'] = max(100, $base * 2);
                } elseif (strpos($name, 'minyak') !== false) {
                    $bahan['stok_maksimum'] = max(40, $base * 3);
                } elseif (in_array($satuan, ['ikat', 'pcs'])) {
                    $bahan['stok_maksimum'] = max(100, $base * 4);
                } else {
                    $bahan['stok_maksimum'] = max($base * 3, $base + 20);
                }
            }

            $bahan['stok_maksimum'] = round(floatval($bahan['stok_maksimum']), 2);
            BahanBaku::create($bahan);
        }
    }
}