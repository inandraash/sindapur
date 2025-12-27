<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\Resep;

class ResepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Menu $menu)
    {
        $search = trim($request->input('search', ''));
        $sortBy = $request->input('sort_by', 'nama_bahan');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $validSorts = ['nama_bahan', 'jumlah_dibutuhkan', 'satuan'];
        if (!in_array($sortBy, $validSorts, true)) {
            $sortBy = 'nama_bahan';
        }

        $bahanBakus = BahanBaku::orderBy('nama_bahan')->get();
        $resepsQuery = $menu->reseps()->with('bahanBaku');

        if ($search !== '') {
            $resepsQuery->whereHas('bahanBaku', function ($q) use ($search) {
                $q->where('nama_bahan', 'like', "%$search%")
                  ->orWhere('satuan', 'like', "%$search%");
            });
        }

        if ($sortBy === 'nama_bahan') {
            $resepsQuery->join('bahan_bakus', 'reseps.bahan_baku_id', '=', 'bahan_bakus.id')
                ->select('reseps.*')
                ->orderBy('bahan_bakus.nama_bahan', $sortDir);
        } elseif ($sortBy === 'satuan') {
            $resepsQuery->join('bahan_bakus', 'reseps.bahan_baku_id', '=', 'bahan_bakus.id')
                ->select('reseps.*')
                ->orderBy('bahan_bakus.satuan', $sortDir);
        } else {
            $resepsQuery->orderBy('jumlah_dibutuhkan', $sortDir);
        }

        $reseps = $resepsQuery->get();
        return view('admin.resep.index', compact('bahanBakus', 'reseps', 'menu', 'search', 'sortBy', 'sortDir'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Menu $menu)
    {
        $request->validate([
            'bahan_baku_id' => ['required', 'exists:bahan_bakus,id'],
            'jumlah_dibutuhkan' => ['required', 'numeric', 'min:0'],
        ]);

        $menu->reseps()->create($request->all());

        return back()->with('success', 'Bahan berhasil ditambahkan ke resep.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resep $resep)
    {
        $request->validate([
            'jumlah_dibutuhkan' => ['required', 'numeric', 'min:0'],
        ]);

        $resep->update([
            'jumlah_dibutuhkan' => $request->jumlah_dibutuhkan,
        ]);

        return back()->with('success', 'Jumlah bahan dalam resep berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resep = Resep::findOrFail($id);
        $resep->delete();

        return back()->with('success', 'Bahan berhasil dihapus dari resep.');
    }
}
