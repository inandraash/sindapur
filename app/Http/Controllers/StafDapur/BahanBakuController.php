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
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $sortBy = $request->input('sort_by', 'nama_bahan');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $validSorts = ['nama_bahan', 'stok_terkini', 'satuan'];
        if (!in_array($sortBy, $validSorts, true)) {
            $sortBy = 'nama_bahan';
        }

        $query = BahanBaku::query();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_bahan', 'like', "%$search%")
                  ->orWhere('satuan', 'like', "%$search%");
            });
        }

        $bahanBakus = $query->orderBy($sortBy, $sortDir)->get();

        return view('staf.bahan-baku.index', compact('bahanBakus', 'search', 'sortBy', 'sortDir'));
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
            'stok_maksimum' => 'nullable|numeric|min:0',
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
            'stok_maksimum' => 'nullable|numeric|min:0',
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
