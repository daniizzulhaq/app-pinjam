<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenor', function (Blueprint $table) {
            $table->id();
            $table->integer('bulan');                  // Jumlah bulan tenor (1, 3, 6, 12, 24, 36)
            $table->string('label');                   // Contoh: "12 Bulan"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('tenor');
    }
};
