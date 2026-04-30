<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request) {
        $search = $request->input('search');
        $items = Barang::when($search, function ($query) use ($search) {
            return $query->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('kode_barang', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('inventaris', compact('items'));
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
        'stok'        => 'required|numeric|min:0', // Validasi stok tidak boleh minus
    ]);

    $item = Barang::findOrFail($id);
    
    // Update semua data termasuk stok
    $item->update([
        'nama_barang' => $request->nama_barang,
        'harga1'      => $request->harga1,
        'stok'        => $request->stok,
        'kategori'    => $request->kategori,
    ]);

    return back()->with('success', 'Stok dan data barang berhasil diperbarui!');
}

    public function destroy($id) {
        Barang::destroy($id);
        return back()->with('success', 'Barang dihapus!');
    }

    public function transaksi(Request $request) {
        $barang = Barang::findOrFail($request->barang_id);
        if ($barang->stok < $request->jumlah) return back()->with('error', 'Stok tipis!');

        DB::transaction(function () use ($barang, $request) {
            $barang->decrement('stok', $request->jumlah);
            Penjualan::create([
                'barang_id' => $barang->id,
                'jumlah' => $request->jumlah,
                'total_harga' => $barang->harga1 * $request->jumlah,
                'tanggal_jual' => now()->format('Y-m-d'),
            ]);
        });
        return back()->with('success', 'Penjualan sukses!');
    }

    public function laporan(Request $request) {
        $view = $request->get('view', 'monthly');
        $data = ($view == 'daily') 
            ? Penjualan::select(DB::raw('tanggal_jual as label'), DB::raw('SUM(total_harga) as total'))->where('tanggal_jual', '>=', now()->subDays(7))->groupBy('label')->get()
            : Penjualan::select(DB::raw('strftime("%m", tanggal_jual) as label'), DB::raw('SUM(total_harga) as total'))->whereYear('tanggal_jual', date('Y'))->groupBy('label')->get();
        return view('laporan', compact('data', 'view'));
    }
}