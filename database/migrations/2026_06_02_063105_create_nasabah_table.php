<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void
    {
        Schema::create('nasabah', function (Blueprint $table) {
            $table->id();
            $table->string('no_ktp', 16)->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir');
            $table->text('alamat');
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota');
            $table->string('provinsi');
            $table->string('no_telepon', 15);
            $table->string('email')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->decimal('penghasilan_bulanan', 15, 2)->nullable();
            $table->string('nama_penjamin')->nullable();
            $table->string('no_telepon_penjamin', 15)->nullable();
            $table->string('hubungan_penjamin')->nullable();
            $table->string('foto_ktp')->nullable();           // path file
            $table->string('foto_nasabah')->nullable();       // path file
            $table->foreignId('user_id')->constrained('users'); // karyawan yang input
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('nasabah');
    }
};