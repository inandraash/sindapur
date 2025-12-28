<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\TransaksiPenjualan;
use App\Models\PemakaianHarian;
use App\Models\TransaksiStok;
use App\Models\User;
use Carbon\Carbon;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup User
        $user = User::whereHas('role', function($q) {
            $q->where('nama_role', 'Staf Dapur');
        })->first() ?? User::first();

        // 2. Setup Menu
        $menus = Menu::with('reseps')->where('harga', '>', 0)->get();
        $nasiRamesDasar = Menu::with('reseps')->where('nama_menu', 'Nasi Rames')->first();

        // 3. Periode Simulasi: 1,5 Bulan (1 Nov - 15 Des 2025)
        $startDate = Carbon::create(2025, 11, 1);
        $endDate   = Carbon::create(2025, 12, 15); // Stop di 15 Desember

        $this->command->info("=== SIMULASI 1,5 BULAN (1 Nov - 15 Des) ===");
        $this->command->info("Catatan: 15 Des (Hari Terakhir) TIDAK ADA BELANJA.");

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            
            $tglStr = $date->format('Y-m-d');
            $isWeekend = $date->isFriday() || $date->isSaturday() || $date->isSunday();

            // ==========================================
            // PHASE 1: BELANJA MINGGUAN (Senin Siang)
            // ==========================================
            
            // Logika: 
            // 1. Belanja jika HARI SENIN ($date->isMonday())
            // 2. ATAU belanja jika HARI PERTAMA ($tglStr == '2025-11-01')
            // 3. TAPI JANGAN belanja jika hari ini 15 Desember ($tglStr != '2025-12-15')
            
            $isLastDay = ($tglStr == '2025-12-15');
            $shouldShop = ($date->isMonday() || $tglStr == '2025-11-01');

            if ($shouldShop && !$isLastDay) {
                $this->belanjaMingguan($date, $user);
            } elseif ($isLastDay) {
                $this->command->warn("  [INFO] Hari ini Senin (15 Des), tapi SKIP belanja karena hari terakhir.");
            }

            // ==========================================
            // PHASE 2: PENJUALAN HARIAN (Tetap Jalan di Tgl 15)
            // ==========================================
            $pemakaianAgregat = [];
            $totalPorsiHariIni = 0;

            foreach ($menus as $menu) {
                // Target Penjualan: Weekdays 5-10, Weekend 12-20
                if ($isWeekend) {
                    $targetJual = rand(12, 20); 
                } else {
                    $targetJual = rand(5, 10);
                }

                $terjual = 0;
                while ($terjual < $targetJual) {
                    $qty = rand(1, 3);
                    
                    if (($terjual + $qty) > $targetJual) {
                        $qty = $targetJual - $terjual;
                    }

                    // Waktu Transaksi: 06.00 - 15.00
                    $waktuTransaksi = $this->randomOperationalTime($date);

                    TransaksiPenjualan::create([
                        'menu_id' => $menu->id,
                        'jumlah_porsi' => $qty,
                        'tanggal_penjualan' => $tglStr,
                        'user_id' => $user->id,
                        'created_at' => $waktuTransaksi,
                        'updated_at' => $waktuTransaksi,
                    ]);

                    $this->kurangiStok($menu, $qty, $nasiRamesDasar, $pemakaianAgregat);
                    $terjual += $qty;
                }
                $totalPorsiHariIni += $terjual;
            }

            // ==========================================
            // PHASE 3: REKAM PEMAKAIAN (SORE)
            // ==========================================
            foreach ($pemakaianAgregat as $bahanId => $totalTerpakai) {
                PemakaianHarian::create([
                    'bahan_baku_id' => $bahanId,
                    'jumlah_terpakai' => $totalTerpakai,
                    'tanggal' => $tglStr,
                    'created_at' => $date->copy()->setTime(15, 30),
                    'updated_at' => $date->copy()->setTime(15, 30),
                ]);
            }

            $this->command->info("[$tglStr] Sales: $totalPorsiHariIni porsi.");
        }
    }

    /**
     * Logika Belanja Mingguan (ANTI OVERSTOCK)
     */
    private function belanjaMingguan($date, $user)
    {
        $bahans = BahanBaku::all();
        
        // Waktu Belanja: Siang (12:30 - 14:00)
        $jamBelanja = rand(12, 13);
        $menitBelanja = ($jamBelanja == 12) ? rand(30, 59) : rand(0, 59);
        $waktuBelanja = $date->copy()->setTime($jamBelanja, $menitBelanja);
        
        $this->command->info("  -> [RESTOCK] Belanja Mingguan pada " . $waktuBelanja->format('H:i'));

        foreach ($bahans as $bahan) {
            $kapasitasGudang = $bahan->stok_maksimum ?? 50;
            $stokSekarang = $bahan->stok_terkini;

            if ($stokSekarang >= ($kapasitasGudang * 0.6)) {
                continue;
            }

            $ruangKosong = $kapasitasGudang - $stokSekarang;
            $jumlahBeli = ceil($ruangKosong); // Isi sampai penuh

            if ($jumlahBeli <= 0) continue;

            $bahan->increment('stok_terkini', $jumlahBeli);

            TransaksiStok::create([
                'bahan_baku_id' => $bahan->id,
                'user_id'       => $user->id,
                'jumlah_masuk'  => $jumlahBeli,
                'tanggal_masuk' => $date->format('Y-m-d'),
                'created_at'    => $waktuBelanja,
                'updated_at'    => $waktuBelanja,
            ]);
        }
    }

    private function randomOperationalTime($date)
    {
        // 40% Sarapan, 40% Siang, 20% Lainnya
        $chance = rand(1, 100);
        if ($chance <= 40) {
            $hour = rand(6, 8);
        } elseif ($chance <= 80) {
            $hour = rand(11, 13);
        } else {
            $pilihan = [9, 10, 14];
            $hour = $pilihan[array_rand($pilihan)];
        }
        return $date->copy()->setTime($hour, rand(0, 59));
    }

    private function kurangiStok($menu, $qty, $nasiRamesDasar, &$pemakaianAgregat)
    {
        $prosesBahan = function($idBahan, $jumlahPerPorsi) use ($qty, &$pemakaianAgregat) {
            $total = $jumlahPerPorsi * $qty;
            $pemakaianAgregat[$idBahan] = ($pemakaianAgregat[$idBahan] ?? 0) + $total;
            BahanBaku::where('id', $idBahan)->decrement('stok_terkini', $total);
        };

        foreach ($menu->reseps as $resep) {
            $prosesBahan($resep->bahan_baku_id, $resep->jumlah_dibutuhkan);
        }

        if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames' && $nasiRamesDasar) {
            foreach ($nasiRamesDasar->reseps as $resepDasar) {
                $prosesBahan($resepDasar->bahan_baku_id, $resepDasar->jumlah_dibutuhkan);
            }
        }
    }
}