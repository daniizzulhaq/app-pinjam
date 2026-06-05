<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pembayaran
            MODIFY COLUMN status
            ENUM('lunas','sebagian','tidak_bayar','bunga')
            NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE pembayaran
            MODIFY COLUMN status
            ENUM('lunas','sebagian','tidak_bayar')
            NOT NULL
        ");
    }
};