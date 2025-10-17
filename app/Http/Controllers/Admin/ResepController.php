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
    public function index(Menu $menu)
    {
        $bahanBakus = BahanBaku::orderby('nama_bahan')->get();
        $reseps = $menu->reseps()->with('bahanBaku')->get();
        return view('admin.resep.index', compact('bahanBakus', 'reseps', 'menu'));
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

        // 2. Update jumlah pada resep yang spesifik
        $resep->update([
            'jumlah_dibutuhkan' => $request->jumlah_dibutuhkan,
        ]);

        // 3. Arahkan kembali dengan pesan sukses
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
