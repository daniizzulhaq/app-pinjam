<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->enum('tenor_tipe', ['harian', 'bulanan'])
                  ->default('bulanan')
                  ->after('tenor_bulan');
        });
    }

    public function down(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropColumn('tenor_tipe');
        });
    }
};