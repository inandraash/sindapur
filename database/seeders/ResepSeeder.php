<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\BahanBaku;

class ResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nasiRames = Menu::where('nama_menu', 'Nasi Rames')->first();
        $nasiRamesTelur = Menu::where('nama_menu', 'Nasi Rames Telur')->first();
        $nasiRamesDaging = Menu::where('nama_menu', 'Nasi Rames Daging')->first();
        $nasiPecel = Menu::where('nama_menu', 'Nasi Pecel')->first();
        $sotoAyam = Menu::where('nama_menu', 'Soto Ayam')->first();
        $asemAsem = Menu::where('nama_menu', 'Asem-Asem Daging')->first();

        $bahanBakus = BahanBaku::pluck('id', 'nama_bahan');

        $nasiRames->reseps()->createMany([
            ['bahan_baku_id' => $bahanBakus['Beras'], 'jumlah_dibutuhkan' => 0.15],
            ['bahan_baku_id' => $bahanBakus['Orek Tempe (Matang)'], 'jumlah_dibutuhkan' => 0.05],
            ['bahan_baku_id' => $bahanBakus['Sambal Terasi'], 'jumlah_dibutuhkan' => 0.02],
            ['bahan_baku_id' => $bahanBakus['Kerupuk Udang'], 'jumlah_dibutuhkan' => 1],
            ['bahan_baku_id' => $bahanBakus['Bawang Goreng'], 'jumlah_dibutuhkan' => 0.003],
            ['bahan_baku_id' => $bahanBakus['Minyak Goreng'], 'jumlah_dibutuhkan' => 0.01],
        ]);
        
        // === Resep Varian (Hanya berisi lauknya) ===
        $nasiRamesTelur = Menu::where('nama_menu', 'Nasi Rames Telur')->first();
        $nasiRamesTelur->reseps()->create(['bahan_baku_id' => $bahanBakus['Telur Ayam'], 'jumlah_dibutuhkan' => 1]);

        $nasiRamesDaging = Menu::where('nama_menu', 'Nasi Rames Daging')->first();
        $nasiRamesDaging->reseps()->create(['bahan_baku_id' => $bahanBakus['Daging Sapi'], 'jumlah_dibutuhkan' => 0.05]);

        $nasiPecel->reseps()->createMany([
            ['bahan_baku_id' => $bahanBakus['Beras'], 'jumlah_dibutuhkan' => 0.15],
            ['bahan_baku_id' => $bahanBakus['Bumbu Pecel (Siap Pakai)'], 'jumlah_dibutuhkan' => 0.07],
            ['bahan_baku_id' => $bahanBakus['Kangkung'], 'jumlah_dibutuhkan' => 0.05],
            ['bahan_baku_id' => $bahanBakus['Tauge'], 'jumlah_dibutuhkan' => 0.03],
            ['bahan_baku_id' => $bahanBakus['Kacang Panjang'], 'jumlah_dibutuhkan' => 0.05],
            ['bahan_baku_id' => $bahanBakus['Peyek Kacang'], 'jumlah_dibutuhkan' => 1],
        ]);
        
        $sotoAyam->reseps()->createMany([
            ['bahan_baku_id' => $bahanBakus['Daging Ayam'], 'jumlah_dibutuhkan' => 0.07],
            ['bahan_baku_id' => $bahanBakus['Bumbu Dasar Soto'], 'jumlah_dibutuhkan' => 0.03],
            ['bahan_baku_id' => $bahanBakus['Telur Ayam'], 'jumlah_dibutuhkan' => 0.5],
            ['bahan_baku_id' => $bahanBakus['Bawang Goreng'], 'jumlah_dibutuhkan' => 0.005],
            ['bahan_baku_id' => $bahanBakus['Minyak Goreng'], 'jumlah_dibutuhkan' => 0.02],
            ['bahan_baku_id' => $bahanBakus['Beras'], 'jumlah_dibutuhkan' => 0.15],
        ]);

        $asemAsem->reseps()->createMany([
            ['bahan_baku_id' => $bahanBakus['Daging Sapi'], 'jumlah_dibutuhkan' => 0.1],
            ['bahan_baku_id' => $bahanBakus['Bumbu Dasar Asem-Asem'], 'jumlah_dibutuhkan' => 0.04],
            ['bahan_baku_id' => $bahanBakus['Buncis'], 'jumlah_dibutuhkan' => 0.05],
            ['bahan_baku_id' => $bahanBakus['Wortel'], 'jumlah_dibutuhkan' => 0.03],
            ['bahan_baku_id' => $bahanBakus['Minyak Goreng'], 'jumlah_dibutuhkan' => 0.02],
            ['bahan_baku_id' => $bahanBakus['Beras'], 'jumlah_dibutuhkan' => 0.15],
        ]);
    }
}
