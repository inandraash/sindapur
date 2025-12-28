<!DOCTYPE html>
<html>
<head>
    <title>{{ $judulLaporan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>RUMAH MAKAN PAK PANGAT</h1>
        <hr>
        <h3>{{ $judulLaporan }}</h3>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            @if($jenisLaporan == 'penjualan')
                <tr>
                    <th width="20%">Tanggal</th>
                    <th>Nama Menu</th>
                    <th width="15%" class="text-center">Jumlah Porsi</th>
                    <th>Dicatat Oleh</th>
                </tr>
            @elseif($jenisLaporan == 'stok_masuk')
                <tr>
                    <th width="20%">Tanggal</th>
                    <th>Nama Bahan</th>
                    <th width="15%" class="text-center">Jumlah Masuk</th>
                    <th width="10%" class="text-center">Satuan</th>
                    <th>Dicatat Oleh</th>
                </tr>
            @else
                <tr>
                    <th width="20%">Tanggal</th>
                    <th>Nama Bahan</th>
                    <th width="20%" class="text-center">Total Pemakaian</th>
                    <th width="10%" class="text-center">Satuan</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse ($dataLaporan as $data)
                <tr>
                    @if($jenisLaporan == 'penjualan')
                        <td>{{ \Carbon\Carbon::parse($data->tanggal_penjualan)->format('d/m/Y') }}</td>
                        <td>{{ $data->menu->nama_menu }}</td>
                        <td class="text-center">{{ number_format($data->jumlah_porsi, 0, ',', '.') }}</td>
                        <td>{{ $data->user->name ?? '-' }}</td>
                    @elseif($jenisLaporan == 'stok_masuk')
                        <td>{{ \Carbon\Carbon::parse($data->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td>{{ $data->bahanBaku->nama_bahan }}</td>
                        <td class="text-center">{{ number_format($data->jumlah_masuk, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $data->bahanBaku->satuan }}</td>
                        <td>{{ $data->user->name ?? '-' }}</td>
                    @else
                        <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $data->bahanBaku->nama_bahan }}</td>
                        <td class="text-center">{{ number_format($data->jumlah_terpakai, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $data->bahanBaku->satuan }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

</body>
</html>