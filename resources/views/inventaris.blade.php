@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-white tracking-tight">Inventaris <span class="text-cyan-400">&</span> Stok</h1>
        <p class="text-gray-500 text-sm">Kelola 18.000+ data barang Barokah Toserba</p>
    </div>
    <div class="flex gap-3">
        <button onclick="openModalForm('tambah')" class="bg-gray-800 border border-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-700 transition font-medium">
            + Barang Baru
        </button>
        <button onclick="document.getElementById('modal-pos').classList.remove('hidden')" class="bg-cyan-600 px-6 py-2.5 rounded-xl font-bold hover:bg-cyan-500 transition shadow-lg shadow-cyan-900/20 text-white">
            ⚡ TRANSAKSI JUAL
        </button>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-900/30 border border-green-500/50 text-green-400 p-4 rounded-2xl mb-6 flex items-center shadow-sm">
        <span class="mr-2">✅</span> {{ session('success') }}
    </div>
@endif

<div class="mb-6">
    <form action="/" method="GET" class="relative">
        <input type="text" name="search" placeholder="Cari Kode atau Nama Barang..." value="{{ request('search') }}" 
               class="w-full bg-gray-900 border border-gray-800 p-4 pl-12 rounded-2xl focus:border-cyan-500 outline-none transition text-white">
        <span class="absolute left-4 top-4 text-gray-600">🔍</span>
    </form>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-3xl overflow-hidden shadow-2xl">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-800/50 text-cyan-400 text-[11px] uppercase tracking-widest font-bold">
            <tr>
                <th class="p-5 border-b border-gray-800">Nama Barang</th>
                <th class="p-5 border-b border-gray-800 text-center">Status Stok</th>
                <th class="p-5 border-b border-gray-800 text-right">Harga Jual</th>
                <th class="p-5 border-b border-gray-800 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            @foreach($items as $item)
            <tr class="hover:bg-gray-800/40 transition group">
                <td class="p-5">
                    <div class="font-semibold text-gray-200">{{ $item->nama_barang }}</div>
                    <div class="text-[10px] text-gray-600 font-mono uppercase">{{ $item->kode_barang }} | {{ $item->kategori }}</div>
                </td>
                <td class="p-5 text-center">
                    @if($item->stok <= 0)
                        <span class="bg-red-950/50 text-red-500 px-3 py-1 rounded-full text-[11px] font-bold border border-red-900">Habis</span>
                    @elseif($item->stok <= 10)
                        <span class="bg-orange-950/50 text-orange-500 px-3 py-1 rounded-full text-[11px] font-bold border border-orange-900">Sisa {{ $item->stok }}</span>
                    @else
                        <span class="text-gray-400 text-sm font-medium">{{ $item->stok }} <span class="text-[10px] text-gray-600 ml-1">Unit</span></span>
                    @endif
                </td>
                <td class="p-5 text-right font-mono text-green-400 font-bold">
                    <span class="text-[10px] text-gray-600 mr-1">Rp</span>{{ number_format($item->harga1, 0, ',', '.') }}
                </td>
                <td class="p-5">
                    <div class="flex justify-center gap-3">
                        <button onclick="openModalForm('edit', {{ $item }})" class="p-2 hover:bg-yellow-500/10 rounded-lg text-yellow-500 transition text-sm">Edit</button>
                        <form action="/barang/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                            @csrf @method('DELETE')
                            <button class="p-2 hover:bg-red-500/10 rounded-lg text-red-500 transition text-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $items->links() }}
</div>

<div id="modal-form" class="hidden fixed inset-0 bg-black/90 flex items-center justify-center p-4 z-[60] backdrop-blur-sm">
    <div class="bg-gray-900 border border-gray-800 p-8 rounded-[2rem] w-full max-w-lg shadow-2xl">
        <div class="flex justify-between items-center mb-8">
            <h2 id="form-title" class="text-2xl font-bold text-white tracking-tight">Update Barang</h2>
            <button onclick="closeModalForm()" class="text-gray-500 hover:text-white transition">&times;</button>
        </div>

        <form id="main-form" method="POST" class="space-y-5">
            @csrf
            <div id="method-spoof"></div>
            
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Kode Barang</label>
                <input type="text" name="kode_barang" id="f-kode" class="w-full bg-black border border-gray-800 p-3.5 rounded-2xl text-white focus:border-cyan-500 outline-none transition" placeholder="Contoh: BRG001">
            </div>

            <div class="space-y-1">
                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap Barang</label>
                <input type="text" name="nama_barang" id="f-nama" class="w-full bg-black border border-gray-800 p-3.5 rounded-2xl text-white focus:border-cyan-500 outline-none transition" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Harga Jual (Rp)</label>
                    <input type="number" name="harga1" id="f-harga" class="w-full bg-black border border-gray-800 p-3.5 rounded-2xl text-white focus:border-cyan-500 outline-none transition" required>
                </div>
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Jumlah Stok</label>
                    <input type="number" name="stok" id="f-stok" class="w-full bg-black border border-gray-800 p-3.5 rounded-2xl text-white focus:border-cyan-500 outline-none transition font-bold" required>
                </div>
            </div>

            <div class="flex gap-3 pt-6">
                <button type="button" onclick="closeModalForm()" class="flex-1 bg-gray-800 text-white py-4 rounded-2xl font-bold hover:bg-gray-700 transition">Batal</button>
                <button type="submit" class="flex-1 bg-cyan-600 text-white py-4 rounded-2xl font-bold hover:bg-cyan-500 transition shadow-lg shadow-cyan-900/40">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalForm(mode, data = null) {
        const modal = document.getElementById('modal-form');
        const form = document.getElementById('main-form');
        const title = document.getElementById('form-title');
        const methodDiv = document.getElementById('method-spoof');

        modal.classList.remove('hidden');
        
        if(mode === 'tambah') {
            title.innerText = "Tambah Barang Baru";
            form.action = "{{ route('barang.store') }}";
            methodDiv.innerHTML = "";
            form.reset();
        } else {
            title.innerText = "Edit & Update Stok";
            form.action = "/barang/" + data.id;
            methodDiv.innerHTML = '@method("PUT")';
            
            // Isi Field
            document.getElementById('f-kode').value = data.kode_barang;
            document.getElementById('f-nama').value = data.nama_barang;
            document.getElementById('f-harga').value = data.harga1;
            document.getElementById('f-stok').value = data.stok;
        }
    }

    function closeModalForm() {
        document.getElementById('modal-form').classList.add('hidden');
    }
</script>
@endsection