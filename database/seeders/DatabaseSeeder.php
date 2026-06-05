<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BungaPinjaman;
use App\Models\Tenor;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Admin ----
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@pinjaman.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // ---- Karyawan ----
        $karyawan = User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'karyawan@pinjaman.com',
            'password'  => Hash::make('password'),
            'role'      => 'karyawan',
            'is_active' => true,
        ]);

        // ---- Bunga ----
        $listBunga = [
            ['nama_bunga' => 'Bunga Flat 1%/bln',    'persentase' => 1.00,  'jenis' => 'flat'],
            ['nama_bunga' => 'Bunga Flat 1.5%/bln',  'persentase' => 1.50,  'jenis' => 'flat'],
            ['nama_bunga' => 'Bunga Flat 2%/bln',    'persentase' => 2.00,  'jenis' => 'flat'],
            ['nama_bunga' => 'Bunga Efektif 2%/bln', 'persentase' => 2.00,  'jenis' => 'efektif'],
            ['nama_bunga' => 'Bunga Flat 30%/bln',   'persentase' => 30.00, 'jenis' => 'flat'],
        ];
        foreach ($listBunga as $b) {
            BungaPinjaman::create($b);
        }
        $bunga30 = BungaPinjaman::where('persentase', 30.00)->first();

        // ---- Tenor Bulanan ----
        foreach ([1, 3, 6, 12, 18, 24, 36] as $bulan) {
            Tenor::create([
                'bulan' => $bulan,
                'label' => $bulan . ' Bulan',
                'tipe'  => 'bulanan',
            ]);
        }

        // ---- Tenor Harian ----
        foreach ([10, 14] as $hari) {
            Tenor::create([
                'bulan' => $hari,
                'label' => $hari . ' Hari',
                'tipe'  => 'harian',
            ]);
        }
        $tenor10Hari = Tenor::where('bulan', 10)->where('tipe', 'harian')->first();

        // ---- Nasabah ----
        $nasabah = Nasabah::create([
            'user_id'             => $karyawan->id,
            'nama_lengkap'        => 'Ahmad Fauzi',
            'no_ktp'              => '1234567890123456',
            'jenis_kelamin'       => 'L',
            'tanggal_lahir'       => '1990-05-15',
            'tempat_lahir'        => 'Kudus',
            'alamat'              => 'Jl. Merdeka No. 10',
            'kelurahan'           => 'Panjunan',
            'kecamatan'           => 'Kota Kudus',
            'kota'                => 'Kudus',
            'provinsi'            => 'Jawa Tengah',
            'no_telepon'          => '081234744455',
            'email'               => 'ahmad@example.com',
            'pekerjaan'           => 'Pedagang',
            'nama_perusahaan'     => '-',
            'penghasilan_bulanan' => 3000000,
            'nama_penjamin'       => 'Siti Aminah',
            'no_telepon_penjamin' => '081298765432',
            'hubungan_penjamin'   => 'Istri',
            'foto_ktp'            => null,
            'foto_nasabah'        => null,
        ]);

        // ---- Pinjaman Harian (jatuh tempo 5 hari lalu → kena denda) ----
        $jumlahPinjaman    = 1000000;
        $totalBunga        = round($jumlahPinjaman * ($bunga30->persentase / 100)); // 300.000
        $totalPinjaman     = $jumlahPinjaman + $totalBunga;                         // 1.300.000
        $tanggalMulai      = Carbon::now()->subDays(15);
        $tanggalJatuhTempo = Carbon::now()->subDays(5);

        Pinjaman::create([
            'no_pinjaman'         => Pinjaman::generateNoPinjaman(),
            'user_id'             => $karyawan->id,
            'nasabah_id'          => $nasabah->id,
            'bunga_id'            => $bunga30->id,
            'tenor_id'            => $tenor10Hari->id,
            'jumlah_pinjaman'     => $jumlahPinjaman,
            'bunga_persen'        => $bunga30->persentase,
            'total_bunga'         => $totalBunga,
            'total_pinjaman'      => $totalPinjaman,
            'cicilan_per_bulan'   => $totalPinjaman,
            'tenor_bulan'         => 10,
            'tenor_tipe'          => 'harian',
            'tanggal_pengajuan'   => Carbon::now()->subDays(16),
            'tanggal_approval'    => Carbon::now()->subDays(15),
            'tanggal_mulai'       => $tanggalMulai,
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'approved_by'         => 1,
            'status'              => 'aktif',
        ]);

        $this->command->info('');
        $this->command->info('✅ Seeder selesai!');
        $this->command->info('   Nasabah     : Ahmad Fauzi');
        $this->command->info('   Pinjaman    : Rp ' . number_format($jumlahPinjaman, 0, ',', '.'));
        $this->command->info('   Bunga 30%   : Rp ' . number_format($totalBunga, 0, ',', '.'));
        $this->command->info('   Total       : Rp ' . number_format($totalPinjaman, 0, ',', '.'));
        $this->command->info('   Tenor       : 10 Hari (harian)');
        $this->command->info('   Jatuh tempo : ' . $tanggalJatuhTempo->format('d M Y') . ' (terlambat 5 hari)');
        $this->command->info('   Denda       : Rp ' . number_format(5 * 50000, 0, ',', '.') . ' (belum dibayar)');
        $this->command->info('');
    }
}