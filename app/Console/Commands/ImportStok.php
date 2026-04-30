<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportStok extends Command
{
    protected $signature = 'import:stok';
    protected $description = 'Import data barang dari tabula-stok.csv ke database';

    public function handle()
    {
        $filePath = app_path('tabula-Stok.csv');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan di: " . $filePath);
            return;
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Melewati baris judul kolom

        $this->info("Memulai proses import... Harap tunggu.");
        
        DB::beginTransaction();
        try {
            $count = 0;
            while (($row = fgetcsv($file, 2000, ",")) !== FALSE) {
                // row[0]=Kode, row[2]=Nama, row[3]=Harga, row[6]=Kategori
                if (empty($row[0])) continue;

                DB::table('barangs')->insert([
                    'kode_barang' => $row[0],
                    'nama_barang' => $row[2] ?? 'Tanpa Nama',
                    'harga1'      => (float) str_replace(',', '', $row[3] ?? 0),
                    'stok'        => rand(10, 100), // Kita isi random dulu karena di CSV biasanya stok kosong
                    'kategori'    => $row[6] ?? 'UMUM',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $count++;
                // Commit setiap 1000 data agar hemat RAM & Cepat
                if ($count % 1000 == 0) {
                    DB::commit();
                    DB::beginTransaction();
                    $this->info("Berhasil mengimpor $count data...");
                }
            }
            DB::commit();
            fclose($file);
            $this->info("SELESAI! Total $count barang berhasil dimasukkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
        }
    }
}