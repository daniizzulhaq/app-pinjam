<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pinjaman
            MODIFY COLUMN status ENUM(
                'menunggu_approval',
                'ditolak',
                'aktif',
                'menunggu_transfer_karyawan',
                'menunggu_transfer_nasabah',
                'lunas'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE pinjaman
            MODIFY COLUMN status ENUM(
                'menunggu_approval',
                'ditolak',
                'aktif',
                'lunas'
            ) NOT NULL
        ");
    }
};