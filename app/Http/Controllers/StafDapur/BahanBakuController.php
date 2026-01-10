<?php

namespace App\Http\Controllers\StafDapur;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'nama_bahan' => 'required|string|max:255|unique:bahan_bakus,nama_bahan',
            'stok_terkini' => 'required|numeric|min:0|lte:stok_maksimum',
            'satuan' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'stok_maksimum' => 'nullable|numeric|min:0.01',
        ], [
            'nama_bahan.unique' => 'Bahan baku sudah terdaftar.',
            'stok_terkini.lte' => 'Stok awal tidak boleh melebihi stok maksimum.',
            'satuan.regex' => 'Satuan hanya boleh berisi huruf.',
            'stok_maksimum.min' => 'Stok maksimum harus lebih dari 0',
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
            'nama_bahan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bahan_bakus', 'nama_bahan')->ignore($id),
            ],
            'stok_terkini' => 'required|numeric|min:0|lte:stok_maksimum',
            'satuan' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
            'stok_maksimum' => 'nullable|numeric|min:0.01',
        ], [
            'nama_bahan.unique' => 'Nama bahan baku sudah digunakan.',
            'stok_terkini.lte' => 'Stok tidak boleh melebihi stok maksimum.',
            'satuan.regex' => 'Satuan hanya boleh berisi huruf.',
            'stok_maksimum.min' => 'Stok maksimum harus lebih dari 0',
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
        
        // Cek apakah bahan baku ini digunakan di resep
        $resepCount = $bahanBaku->reseps()->count();
        if ($resepCount > 0) {
            return redirect()
                ->route('staf.bahan-baku.index')
                ->with('error', "Bahan baku \"{$bahanBaku->nama_bahan}\" tidak bisa dihapus karena masih digunakan di {$resepCount} resep menu. Silakan hapus atau ganti bahan di resep terkait terlebih dahulu.");
        }
        
        // Hapus akan otomatis cascade ke transaksi_stoks dan pemakaian_harians (data historis)
        $bahanBaku->delete();

        return redirect()->route('staf.bahan-baku.index')->with('success', 'Bahan baku berhasil dihapus.');
    }

    /**
     * Store multiple bahan baku in one submission.
     */
    public function bulkStore(Request $request)
    {
        $rawItems = collect($request->input('items', []))
            ->filter(function ($item) {
                return filled($item['nama_bahan'] ?? null)
                    || filled($item['stok_terkini'] ?? null)
                    || filled($item['satuan'] ?? null)
                    || filled($item['stok_maksimum'] ?? null);
            })
            ->values()
            ->all();

        $request->merge(['items' => $rawItems]);

        $data = $request->validateWithBag(
            'bulkAdd',
            [
                'items' => 'required|array|min:1',
                'items.*.nama_bahan' => [
                    'required',
                    'string',
                    'max:255',
                    'distinct',
                    Rule::unique('bahan_bakus', 'nama_bahan'),
                ],
                'items.*.stok_terkini' => 'required|numeric|min:0|lte:items.*.stok_maksimum',
                'items.*.satuan' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-]+$/'],
                'items.*.stok_maksimum' => 'nullable|numeric|min:0.01',
            ],
            [
                'items.required' => 'Tambahkan minimal satu bahan baku.',
                'items.*.nama_bahan.required' => 'Nama bahan wajib diisi.',
                'items.*.nama_bahan.unique' => 'Bahan baku sudah terdaftar.',
                'items.*.nama_bahan.distinct' => 'Nama bahan baku tidak boleh duplikat dalam daftar.',
                'items.*.stok_terkini.required' => 'Stok awal wajib diisi.',
                'items.*.stok_terkini.min' => 'Stok awal tidak boleh negatif.',
                'items.*.stok_terkini.lte' => 'Stok awal tidak boleh melebihi stok maksimum.',
                'items.*.satuan.required' => 'Satuan wajib diisi.',
                'items.*.satuan.regex' => 'Satuan hanya boleh berisi huruf.',
                'items.*.stok_maksimum.min' => 'Stok maksimum harus lebih dari 0.',
            ]
        );

        foreach ($data['items'] as $item) {
            BahanBaku::create([
                'nama_bahan' => $item['nama_bahan'],
                'stok_terkini' => $item['stok_terkini'],
                'satuan' => $item['satuan'],
                'stok_maksimum' => $item['stok_maksimum'] ?? null,
            ]);
        }

        return redirect()
            ->route('staf.bahan-baku.index')
            ->with('success', 'Berhasil menambahkan ' . count($data['items']) . ' bahan baku sekaligus.');
    }

    /**
     * Delete multiple bahan baku at once.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:bahan_bakus,id',
        ], [
            'ids.required' => 'Pilih minimal satu bahan baku yang akan dihapus.',
        ]);

        $bahanBakus = BahanBaku::whereIn('id', $validated['ids'])->get();

        $blocked = [];
        foreach ($bahanBakus as $bahanBaku) {
            $resepCount = $bahanBaku->reseps()->count();
            if ($resepCount > 0) {
                $blocked[] = "{$bahanBaku->nama_bahan} (dipakai di {$resepCount} resep)";
            }
        }

        if (!empty($blocked)) {
            return redirect()
                ->route('staf.bahan-baku.index')
                ->with('error', 'Beberapa bahan tidak bisa dihapus: ' . implode(', ', $blocked));
        }

        foreach ($bahanBakus as $bahanBaku) {
            $bahanBaku->delete();
        }

        return redirect()
            ->route('staf.bahan-baku.index')
            ->with('success', 'Berhasil menghapus ' . count($bahanBakus) . ' bahan baku.');
    }
}
