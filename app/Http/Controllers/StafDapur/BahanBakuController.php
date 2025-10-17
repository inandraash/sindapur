<?php

namespace App\Http\Controllers\StafDapur;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bahanBakus = BahanBaku::latest()->get();
        return view('staf.bahan-baku.index', compact('bahanBakus'));
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
    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'stok_terkini' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        BahanBaku::create($request->all());

        return redirect()->route('staf.bahan-baku.index')->with('success', 'Bahan baku berhasil ditambahkan.');
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
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'stok_terkini' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
        ]);

        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->update($request->all());

        return redirect()->route('staf.bahan-baku.index')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->delete();

        return redirect()->route('staf.bahan-baku.index')->with('success', 'Bahan baku berhasil dihapus.');
    }
}
