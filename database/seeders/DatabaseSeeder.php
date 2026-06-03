<?php

namespace Database\Seeders;
 
use App\Models\User;
use App\Models\BungaPinjaman;
use App\Models\Tenor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
 
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Buat Admin ----
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@pinjaman.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);
 
        // ---- Buat Karyawan Contoh ----
        User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'karyawan@pinjaman.com',
            'password'  => Hash::make('password'),
            'role'      => 'karyawan',
            'is_active' => true,
        ]);
 
        // ---- Data Bunga ----
        $bunga = [
            ['nama_bunga' => 'Bunga Flat 1%/bln', 'persentase' => 1.00, 'jenis' => 'flat'],
            ['nama_bunga' => 'Bunga Flat 1.5%/bln', 'persentase' => 1.50, 'jenis' => 'flat'],
            ['nama_bunga' => 'Bunga Flat 2%/bln', 'persentase' => 2.00, 'jenis' => 'flat'],
            ['nama_bunga' => 'Bunga Efektif 2%/bln', 'persentase' => 2.00, 'jenis' => 'efektif'],
        ];
        foreach ($bunga as $b) {
            BungaPinjaman::create($b);
        }
 
        // ---- Data Tenor ----
        $tenors = [1, 3, 6, 12, 18, 24, 36];
        foreach ($tenors as $bulan) {
            Tenor::create([
                'bulan' => $bulan,
                'label' => $bulan . ' Bulan',
            ]);
        }
    }
}