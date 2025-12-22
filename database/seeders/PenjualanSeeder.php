<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\TransaksiPenjualan;
use App\Models\PemakaianHarian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil User Staf Dapur (atau user pertama) untuk dicatat sebagai pencatat
        $user = User::whereHas('role', function($q) {
            $q->where('nama_role', 'Staf Dapur');
        })->first() ?? User::first();

        // 2. Ambil Menu Dasar untuk logika Nasi Rames
        $nasiRamesDasar = Menu::where('nama_menu', 'Nasi Rames')->first();

        // 3. Tentukan Periode Tanggal: dari kemarin mundur 7 hari (termasuk kemarin)
        $endDate = Carbon::yesterday();
        $startDate = $endDate->copy()->subDays(6);

        // 4. Ambil semua menu yang dijual
        $menus = Menu::where('harga', '>', 0)->get();

        // Mulai Loop per Hari
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            
            $this->command->info("Memproses penjualan tanggal: " . $date->format('Y-m-d'));
            
            // Array untuk menampung total pemakaian bahan hari ini
            $pemakaianAgregat = [];

            // Simulasi penjualan untuk setiap menu
            foreach ($menus as $menu) {
                // Generate jumlah porsi acak (misal: antara 5 sampai 25 porsi)
                $requestedPorsi = rand(5, 25);

                // Hitung berapa porsi maksimal yang bisa dibuat berdasarkan stok saat ini
                $maxPossible = PHP_INT_MAX;

                foreach ($menu->reseps as $resep) {
                    $bahan = BahanBaku::find($resep->bahan_baku_id);
                    if (!$bahan) continue;

                    $need = floatval($resep->jumlah_dibutuhkan ?: 0);
                    if ($need <= 0) continue;

                    $possible = floor(floatval($bahan->stok_terkini) / $need);
                    $maxPossible = min($maxPossible, max(0, $possible));
                }

                // Jika menu modular Nasi Rames, pertimbangkan juga resep dasar
                if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames' && $nasiRamesDasar) {
                    foreach ($nasiRamesDasar->reseps as $resepDasar) {
                        $bahan = BahanBaku::find($resepDasar->bahan_baku_id);
                        if (!$bahan) continue;

                        $need = floatval($resepDasar->jumlah_dibutuhkan ?: 0);
                        if ($need <= 0) continue;

                        $possible = floor(floatval($bahan->stok_terkini) / $need);
                        $maxPossible = min($maxPossible, max(0, $possible));
                    }
                }

                if ($maxPossible === PHP_INT_MAX) {
                    // tidak ada resep (mis. menu kosong), skip
                    continue;
                }

                if ($maxPossible <= 0) {
                    // Tidak ada stok untuk menu ini hari ini
                    continue;
                }

                $jumlahPorsi = min($requestedPorsi, (int)$maxPossible);

                if ($jumlahPorsi <= 0) {
                    continue;
                }

                // A. Catat Transaksi Penjualan (jumlah porsi telah disesuaikan agar stok tidak negatif)
                TransaksiPenjualan::create([
                    'menu_id' => $menu->id,
                    'jumlah_porsi' => $jumlahPorsi,
                    'tanggal_penjualan' => $date->format('Y-m-d'),
                    'user_id' => $user->id,
                    'created_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)), // Jam acak
                    'updated_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                ]);

                // B. Hitung Pemakaian Bahan (Resep Utama) dan kurangi stok segera
                foreach ($menu->reseps as $resep) {
                    $total = $resep->jumlah_dibutuhkan * $jumlahPorsi;
                    $pemakaianAgregat[$resep->bahan_baku_id] = ($pemakaianAgregat[$resep->bahan_baku_id] ?? 0) + $total;

                    // Kurangi stok segera agar menu berikutnya mempertimbangkan perubahan stok
                    $b = BahanBaku::find($resep->bahan_baku_id);
                    if ($b) {
                        $b->decrement('stok_terkini', $total);
                    }
                }

                // C. Hitung Pemakaian Bahan (Resep Modular Nasi Rames)
                if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames') {
                    if ($nasiRamesDasar) {
                        foreach ($nasiRamesDasar->reseps as $resepDasar) {
                            $totalDasar = $resepDasar->jumlah_dibutuhkan * $jumlahPorsi;
                            $pemakaianAgregat[$resepDasar->bahan_baku_id] = ($pemakaianAgregat[$resepDasar->bahan_baku_id] ?? 0) + $totalDasar;

                            $b2 = BahanBaku::find($resepDasar->bahan_baku_id);
                            if ($b2) {
                                $b2->decrement('stok_terkini', $totalDasar);
                            }
                        }
                    }
                }
            }

            // 5. Simpan Data Pemakaian Harian (stok sudah dikurangi saat pemrosesan per menu)
            foreach ($pemakaianAgregat as $bahanId => $totalTerpakai) {
                if ($totalTerpakai <= 0) continue;

                PemakaianHarian::create([
                    'bahan_baku_id' => $bahanId,
                    'jumlah_terpakai' => $totalTerpakai,
                    'tanggal' => $date->format('Y-m-d'),
                    'created_at' => $date->copy()->setTime(21, 0), // Anggap diinput malam hari
                    'updated_at' => $date->copy()->setTime(21, 0),
                ]);
            }
        }
    }
}