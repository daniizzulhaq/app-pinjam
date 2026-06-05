<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_admin', function (Blueprint $table) {
            $table->id();

            // Identitas Lembaga
            $table->string('nama_lembaga')->default('Pinjamin');
            $table->string('tagline')->nullable();
            $table->string('logo')->nullable();           // path file logo/foto
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();

            // Rekening Bank (bisa lebih dari 1, disimpan sebagai JSON)
            $table->json('rekening')->nullable();
            /*
             * Format JSON rekening:
             * [
             *   {
             *     "bank"      : "BCA",
             *     "no_rek"    : "1234567890",
             *     "atas_nama" : "Pinjamin Indonesia"
             *   },
             *   ...
             * ]
             */

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_admin');
    }
};