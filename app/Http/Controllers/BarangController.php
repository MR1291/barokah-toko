<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    // Halaman Inventaris Utama
    public function index(Request $request) {
        $search = $request->input('search');
        $items = Barang::when($search, function ($query) use ($search) {
            return $query->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('kode_barang', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('inventaris', compact('items'));
    }

    // Fungsi POS Baru (Untuk menampilkan halaman kasir)
    public function pos() {
        // Mengambil semua barang untuk pilihan di kasir
        $items = Barang::where('stok', '>', 0)->get(); 
        return view('pos', compact('items'));
    }

    public function store(Request $request) {
        Barang::create($request->all());
        return back()->with('success', 'Barang berhasil ditambah!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga1'      => 'required|numeric',
            'stok'        => 'required|numeric|min:0',
        ]);

        $item = Barang::findOrFail($id);
        
        $item->update([
            'nama_barang' => $request->nama_barang,
            'harga1'      => $request->harga1,
            'stok'        => $request->stok,
            'kategori'    => $request->kategori,
            'kode_barang' => $request->kode_barang,
        ]);

        return back()->with('success', 'Stok dan data barang berhasil diperbarui!');
    }

    public function destroy($id) {
        Barang::destroy($id);
        return back()->with('success', 'Barang dihapus!');
    }

    // Update Fungsi Transaksi: Mendukung Jual Cepat & POS
   public function transaksi(Request $request) {
    // 1. Ambil data cart dari request (dikirim via AJAX dari halaman POS)
    $cart = $request->input('cart'); 

    if (!$cart || count($cart) == 0) {
        return response()->json(['message' => 'Keranjang kosong!'], 400);
    }

    try {
        DB::transaction(function () use ($cart) {
            foreach ($cart as $item) {
                $barang = Barang::findOrFail($item['id']);

                // 2. Cek apakah stok cukup
                if ($barang->stok < $item['qty']) {
                    throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi!");
                }

                // 3. Kurangi stok barang
                $barang->decrement('stok', $item['qty']);

                // 4. Catat ke tabel penjualan agar Laporan otomatis terupdate
                Penjualan::create([
                    'barang_id' => $barang->id,
                    'jumlah' => $item['qty'],
                    'total_harga' => $barang->harga1 * $item['qty'],
                    'tanggal_jual' => now(), // Mengambil waktu sekarang
                ]);
            }
        });

        return response()->json(['message' => 'Transaksi Berhasil & Stok Terupdate!']);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }


        // Cek apakah ini transaksi dari POS (Array Barang)
        if ($request->has('cart')) {
            DB::transaction(function () use ($request) {
                foreach ($request->cart as $item) {
                    $barang = Barang::findOrFail($item['id']);
                    $barang->decrement('stok', $item['qty']);
                    
                    Penjualan::create([
                        'barang_id' => $barang->id,
                        'jumlah' => $item['qty'],
                        'total_harga' => $barang->harga1 * $item['qty'],
                        'tanggal_jual' => now()->format('Y-m-d'),
                    ]);
                }
            });
            return response()->json(['message' => 'Transaksi POS Berhasil']);
        }
        
        return back()->with('error', 'Data transaksi tidak valid');
    }
    public function laporan(Request $request)
{
    // 1. Ambil inputan filter dari user (jika tidak ada, default hari ini)
    $filterType = $request->input('filter_type', 'hari_ini'); 
    $selectedDate = $request->input('tanggal'); // format: YYYY-MM-DD
    $selectedMonth = $request->input('bulan', date('m')); 
    $selectedYear = $request->input('tahun', date('Y'));

    // Query dasar untuk mengambil data transaksi / penjualan
    // (Asumsi kamu punya tabel 'transaksis' atau 'penjualans' yang mencatat total harga penjualan)
    $queryPenjualan = DB::table('transaksis'); 

    // 2. Logika Pemrosesan Filter Waktu
    if ($filterType === 'hari_ini') {
        $queryPenjualan->whereDate('created_at', now()->today());
        $labelWaktu = "Hari Ini (" . now()->format('d M Y') . ")";
    } elseif ($filterType === 'kustom_tanggal' && $selectedDate) {
        $queryPenjualan->whereDate('created_at', $selectedDate);
        $labelWaktu = "Tanggal " . date('d M Y', strtotime($selectedDate));
    } elseif ($filterType === 'bulanan') {
        $queryPenjualan->whereMonth('created_at', $selectedMonth)
                       ->whereYear('created_at', $selectedYear);
        $namaBulan = date('F', mktime(0, 0, 0, $selectedMonth, 10));
        $labelWaktu = "Bulan $namaBulan $selectedYear";
    } else {
        // Fallback jika tidak ada filter cocok
        $queryPenjualan->whereDate('created_at', now()->today());
        $labelWaktu = "Hari Ini (" . now()->format('d M Y') . ")";
    }

    // Hitung Total Omset & Total Transaksi berdasarkan filter di atas
    $totalPenjualan = $queryPenjualan->sum('total_harga'); 
    $totalTransaksiCount = $queryPenjualan->count();
    
    // Ambil list detail item transaksi terlaris/terbaru untuk ditampilkan di tabel laporan
    $listPenjualan = $queryPenjualan->latest()->paginate(10)->withQueryString();

    // 3. FITUR STOK MENIPIS (Ambil produk yang stoknya <= 10)
    // Otomatis kasih warning buat admin toko
    $stokKritis = Barang::where('stok', '<=', 10)
                        ->orderBy('stok', 'asc')
                        ->get();

    // 4. Oper semua data ke view laporan
    return view('laporan', compact(
        'totalPenjualan', 
        'totalTransaksiCount', 
        'listPenjualan', 
        'stokKritis', 
        'labelWaktu',
        'filterType',
        'selectedDate',
        'selectedMonth',
        'selectedYear'
    ));
}


    public function destroyBulk(Request $request) {
        $ids = $request->ids;
        if ($ids) {
            Barang::whereIn('id', $ids)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Tidak ada data'], 400);
    }
}