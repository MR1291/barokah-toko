@extends('layouts.app')

@section('content')
<div class="space-y-8">
    
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-cyan-400">📊 Laporan Penjualan</h1>
            <p class="text-gray-400 text-sm mt-1">Tracking omset kasir dan monitoring sisa stok gudang</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 px-4 py-2 rounded-xl text-sm font-medium text-cyan-300">
            Periode Aktif: {{ $labelWaktu }}
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl shadow-xl">
        <h2 class="text-md font-semibold text-gray-300 mb-4">⚙️ Atur Saringan Tracking Laporan</h2>
        
        <form action="{{ route('laporan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">Jenis Tracking</label>
                <select name="filter_type" onchange="this.form.submit()" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
                    <option value="hari_ini" {{ $filterType == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="kustom_tanggal" {{ $filterType == 'kustom_tanggal' ? 'selected' : '' }}>Kustom Per Tanggal</option>
                    <option value="bulanan" {{ $filterType == 'bulanan' ? 'selected' : '' }}>Bulanan (Pilih Bulan)</option>
                </select>
            </div>

            @if($filterType === 'kustom_tanggal')
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ $selectedDate }}" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-cyan-500">
            </div>
            @endif

            @if($filterType === 'bulanan')
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">Bulan</label>
                <select name="bulan" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">Tahun</label>
                <select name="tahun" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-500">
                    @for($y=date('Y'); $y>=2024; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            @endif

            <div>
                <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm">
                    🔍 Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <p class="text-sm text-gray-400 font-medium">💰 Total Omset Hasil Penjualan</p>
                <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
            </div>
            <div class="text-3xl p-3 bg-emerald-950/40 border border-emerald-500/20 text-emerald-400 rounded-xl">💸</div>
        </div>
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <p class="text-sm text-gray-400 font-medium">🛒 Total Nota / Transaksi Sukses</p>
                <h3 class="text-3xl font-extrabold text-cyan-400 mt-2">{{ $totalTransaksiCount }} Transaksi</h3>
            </div>
            <div class="text-3xl p-3 bg-cyan-950/40 border border-cyan-500/20 text-cyan-400 rounded-xl">📝</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-lg font-bold text-gray-200 mb-4">📋 Log Histori Penjualan</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-800 text-gray-400">
                            <th class="pb-3 font-semibold">No. Nota</th>
                            <th class="pb-3 font-semibold">Tanggal</th>
                            <th class="pb-3 font-semibold text-right">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/50">
                        @forelse($listPenjualan as $tx)
                        <tr class="text-gray-300 hover:bg-gray-850/50 transition">
                            <td class="py-3 font-mono text-cyan-500">#{{ $tx->id ?? 'TX-'.$loop->iteration }}</td>
                            <td class="py-3 text-xs">{{ date('d M Y H:i', strtotime($tx->created_at)) }}</td>
                            <td class="py-3 text-right font-semibold text-emerald-400">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-500 text-xs">Belum ada transaksi terekam pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $listPenjualan->links() }}
            </div>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl h-fit">
            <div class="flex items-center space-x-2 text-red-400 mb-4">
                <span>⚠️</span>
                <h2 class="text-md font-bold text-gray-200">Stok Mulai Menipis (&le; 10)</h2>
            </div>
            
            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                @forelse($stokKritis as $brg)
                <div class="p-3 bg-red-950/20 border border-red-500/20 rounded-xl flex justify-between items-center">
                    <div class="max-w-[70%]">
                        <h4 class="text-sm font-semibold text-gray-300 truncate">{{ $brg->nama_barang }}</h4>
                        <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $brg->kode_barang }}</p>
                    </div>
                    <div class="text-center">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-500/10 text-red-400 border border-red-500/30">
                            Sisa: {{ $brg->stok }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500 text-xs bg-gray-950 rounded-xl border border-gray-800/40">
                    ✅ Aman Bos! Semua stok barang di gudang melimpah ruah.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection 