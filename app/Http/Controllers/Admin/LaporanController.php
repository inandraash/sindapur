<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PemakaianHarian;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiStok;
use App\Models\Menu;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::today()->toDateString());
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::today()->subDays(29)->toDateString());
        $jenisLaporan = $request->input('jenis_laporan', 'penjualan');
        $search = trim($request->input('search', ''));
        $sortBy = $request->input('sort_by', $jenisLaporan === 'penjualan' ? 'total' : 'total');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $dataLaporan = collect();
        $kolomTabel = [];

        if ($jenisLaporan == 'penjualan') {
            $kolomTabel = ['Nama Menu', 'Total Porsi Terjual'];

            $agregat = TransaksiPenjualan::whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir])
                ->select('menu_id', DB::raw('SUM(jumlah_porsi) as total_porsi'))
                ->groupBy('menu_id')
                ->pluck('total_porsi', 'menu_id');

            $dataLaporan = Menu::orderBy('nama_menu')
                ->get()
                ->map(function ($menu) use ($agregat) {
                    $row = new \stdClass();
                    $row->menu = $menu;
                    $row->total = (int) ($agregat[$menu->id] ?? 0);
                    return $row;
                });

            if ($search !== '') {
                $dataLaporan = $dataLaporan->filter(function ($r) use ($search) {
                    return stripos($r->menu->nama_menu, $search) !== false;
                });
            }

            $dataLaporan = ($sortBy === 'nama')
                ? ($sortDir === 'asc' ? $dataLaporan->sortBy(fn($r) => strtolower($r->menu->nama_menu)) : $dataLaporan->sortByDesc(fn($r) => strtolower($r->menu->nama_menu)))
                : ($sortDir === 'asc' ? $dataLaporan->sortBy('total') : $dataLaporan->sortByDesc('total'));

            $dataLaporan = $dataLaporan->values();

        } elseif ($jenisLaporan == 'stok_masuk') {
            $kolomTabel = ['Nama Bahan Baku', 'Total Masuk', 'Satuan'];

            $dataLaporan = TransaksiStok::with('bahanBaku')
                ->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalAkhir])
                ->select('bahan_baku_id', DB::raw('SUM(jumlah_masuk) as total'))
                ->groupBy('bahan_baku_id')
                ->get();

            if ($search !== '') {
                $dataLaporan = $dataLaporan->filter(function ($r) use ($search) {
                    return $r->bahanBaku && stripos($r->bahanBaku->nama_bahan, $search) !== false;
                });
            }

            $dataLaporan = ($sortBy === 'nama')
                ? ($sortDir === 'asc' ? $dataLaporan->sortBy(fn($r) => strtolower(optional($r->bahanBaku)->nama_bahan)) : $dataLaporan->sortByDesc(fn($r) => strtolower(optional($r->bahanBaku)->nama_bahan)))
                : ($sortDir === 'asc' ? $dataLaporan->sortBy('total') : $dataLaporan->sortByDesc('total'));

            $dataLaporan = $dataLaporan->values();

        } elseif ($jenisLaporan == 'pemakaian') {
            $kolomTabel = ['Nama Bahan Baku', 'Total Pemakaian', 'Satuan'];
            $dataLaporan = PemakaianHarian::with('bahanBaku')
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
                ->select('bahan_baku_id', DB::raw('SUM(jumlah_terpakai) as total'))
                ->groupBy('bahan_baku_id')
                ->get();

            if ($search !== '') {
                $dataLaporan = $dataLaporan->filter(function ($r) use ($search) {
                    return $r->bahanBaku && stripos($r->bahanBaku->nama_bahan, $search) !== false;
                });
            }

            $dataLaporan = ($sortBy === 'nama')
                ? ($sortDir === 'asc' ? $dataLaporan->sortBy(fn($r) => strtolower(optional($r->bahanBaku)->nama_bahan)) : $dataLaporan->sortByDesc(fn($r) => strtolower(optional($r->bahanBaku)->nama_bahan)))
                : ($sortDir === 'asc' ? $dataLaporan->sortBy('total') : $dataLaporan->sortByDesc('total'));

            $dataLaporan = $dataLaporan->values();
        }

        return view('admin.laporan.index', compact(
            'dataLaporan', 
            'jenisLaporan', 
            'tanggalMulai', 
            'tanggalAkhir',
            'kolomTabel',
            'search',
            'sortBy',
            'sortDir'
        ));
    }

    public function download(Request $request)
    {
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::today()->toDateString());
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::today()->toDateString());
        $jenisLaporan = $request->input('jenis_laporan', 'penjualan');

        $dataLaporan = [];
        $judulLaporan = ''; 

        if ($jenisLaporan == 'penjualan') {
            $judulLaporan = 'Laporan Transaksi Penjualan';
            $dataLaporan = TransaksiPenjualan::with(['menu', 'user'])
                ->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('tanggal_penjualan', 'asc') // default fallback
                ->get()
                ->sortBy(function ($r) {
                    $tanggal = $r->tanggal_penjualan ?? '';
                    $menu = $r->menu ? strtolower($r->menu->nama_menu) : '';
                    return $tanggal . '|' . $menu;
                })
                ->values();

        } elseif ($jenisLaporan == 'stok_masuk') {
            $judulLaporan = 'Laporan Transaksi Stok Masuk';
            $dataLaporan = TransaksiStok::with(['bahanBaku', 'user'])
                ->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('tanggal_masuk', 'asc')
                ->get();

        } elseif ($jenisLaporan == 'pemakaian') {
            $judulLaporan = 'Laporan Transaksi Pemakaian Bahan';
            $dataLaporan = PemakaianHarian::with('bahanBaku')
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'dataLaporan', 
            'jenisLaporan', 
            'tanggalMulai', 
            'tanggalAkhir',
            'judulLaporan'
        ));

        $pdf->setPaper('A4', 'portrait');

        $fileName = 'laporan-' . $jenisLaporan . '-' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($fileName);
    }
}