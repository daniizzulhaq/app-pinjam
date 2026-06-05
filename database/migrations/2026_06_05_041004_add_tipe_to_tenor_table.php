<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('tenor', function (Blueprint $table) {
        $table->enum('tipe', ['harian', 'bulanan'])->default('bulanan')->after('bulan');
    });
}

public function down(): void
{
    Schema::table('tenor', function (Blueprint $table) {
        $table->dropColumn('tipe');
    });
}
};
