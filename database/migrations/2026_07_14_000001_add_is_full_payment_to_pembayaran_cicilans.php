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
        Schema::table('pembayaran_cicilans', function (Blueprint $table) {
            $table->boolean('is_full_payment')->default(false)->after('cicilan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_cicilans', function (Blueprint $table) {
            $table->dropColumn('is_full_payment');
        });
    }
};
