<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {

            $table->string('bukti_transfer_admin')
                  ->nullable()
                  ->after('status');

            $table->timestamp('tgl_transfer_admin')
                  ->nullable()
                  ->after('bukti_transfer_admin');

            $table->string('bukti_transfer_karyawan')
                  ->nullable()
                  ->after('tgl_transfer_admin');

            $table->timestamp('tgl_transfer_karyawan')
                  ->nullable()
                  ->after('bukti_transfer_karyawan');
        });
    }

    public function down(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {

            $table->dropColumn([
                'bukti_transfer_admin',
                'tgl_transfer_admin',
                'bukti_transfer_karyawan',
                'tgl_transfer_karyawan',
            ]);
        });
    }
};