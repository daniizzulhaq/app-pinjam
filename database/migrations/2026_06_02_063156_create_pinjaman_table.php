<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('no_pinjaman')->unique();           // Auto-generate: PIN-2024-0001
            $table->foreignId('nasabah_id')->constrained('nasabah');
            $table->foreignId('user_id')->constrained('users'); // karyawan pengaju
            $table->foreignId('bunga_id')->constrained('bunga_pinjaman');
            $table->foreignId('tenor_id')->constrained('tenor');
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('bunga_persen', 5, 2);            // snapshot bunga saat pengajuan
            $table->integer('tenor_bulan');                   // snapshot tenor saat pengajuan
            $table->decimal('cicilan_per_bulan', 15, 2);
            $table->decimal('total_pinjaman', 15, 2);         // pokok + total bunga
            $table->decimal('total_bunga', 15, 2);
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_approval')->nullable();
            $table->date('tanggal_mulai')->nullable();        // setelah diapprove
            $table->date('tanggal_jatuh_tempo')->nullable();  // tanggal_mulai + tenor
            $table->enum('status', [
                'menunggu_approval',
                'disetujui',
                'ditolak',
                'aktif',
                'lunas',
            ])->default('menunggu_approval');
            $table->text('catatan_pengajuan')->nullable();
            $table->text('catatan_approval')->nullable();     // alasan approve/reject
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};