<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_bank')->nullable()->after('is_active');
            $table->string('no_rekening')->nullable()->after('nama_bank');
            $table->string('nama_pemilik_rekening')->nullable()->after('no_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'no_rekening', 'nama_pemilik_rekening']);
        });
    }
};