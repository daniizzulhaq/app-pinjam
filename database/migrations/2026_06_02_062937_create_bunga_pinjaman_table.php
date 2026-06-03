<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void
    {
        Schema::create('bunga_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bunga');              // Contoh: "Bunga Flat 2%"
            $table->decimal('persentase', 5, 2);       // Contoh: 2.50
            $table->enum('jenis', ['flat', 'efektif'])->default('flat');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('bunga_pinjaman');
    }
};