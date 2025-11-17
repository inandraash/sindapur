<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BahanBaku;
use App\Models\PemakaianHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard yang relevan berdasarkan role pengguna.
     */
    public function index(Request $request)
    {
        $role = Auth::user()->role->nama_role;
        $viewData = [];

        // ----------------- DATA UNTUK ADMIN -----------------
        if ($role == 'Admin') {
            // ... (Logika data Admin Anda) ...
        
        // ----------------- DATA UNTUK STAF DAPUR -----------------
        } elseif ($role == 'Staf Dapur') {
            $ambangBatasKritis = 10; 
            $viewData['stokKritis'] = BahanBaku::where('stok_terkini', '<=', $ambangBatasKritis)
                                        ->orderBy('stok_terkini', 'asc')
                                        ->limit(5)
                                        ->get();

            $tanggalMulai = Carbon::today()->subDays(6);
            $tanggalSelesai = Carbon::today();
            
            $dataGrafikQuery = PemakaianHarian::with('bahanBaku')
                                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
                                    ->select('bahan_baku_id', DB::raw('SUM(jumlah_terpakai) as total_pemakaian'))
                                    ->groupBy('bahan_baku_id')
                                    ->orderBy('total_pemakaian', 'desc')
                                    ->limit(5)
                                    ->get();
            
            $viewData['chartLabels'] = $dataGrafikQuery->map(fn($data) => $data->bahanBaku->nama_bahan);
            $viewData['chartData'] = $dataGrafikQuery->map(fn($data) => $data->total_pemakaian);
        }

        return view('dashboard', $viewData);
    }
}