<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('no_pembayaran')->unique();         // PAY-2024-0001
            $table->foreignId('pinjaman_id')->constrained('pinjaman');
            $table->foreignId('user_id')->constrained('users'); // karyawan yang input
            $table->integer('angsuran_ke');                   // cicilan ke-berapa
            $table->date('tanggal_jatuh_tempo_cicilan');
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_cicilan', 15, 2);         // seharusnya dibayar
            $table->decimal('jumlah_dibayar', 15, 2);         // yang dibayar
            $table->decimal('pokok_dibayar', 15, 2);
            $table->decimal('bunga_dibayar', 15, 2);
            $table->decimal('denda', 15, 2)->default(0);      // jika terlambat
            $table->integer('hari_terlambat')->default(0);
            $table->enum('jenis_pembayaran', [
                'bayar_lunas',
                'bayar_bunga_saja',
                'tidak_bayar',
                'cicilan_normal',
            ])->default('cicilan_normal');
            $table->enum('status', ['lunas', 'sebagian', 'tidak_bayar'])->default('lunas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};