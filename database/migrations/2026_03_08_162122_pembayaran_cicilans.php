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
        Schema::create('pembayaran_cicilans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cicilan_id')
                ->constrained('cicilans')
                ->onDelete('cascade');

            $table->string('bukti_transfer');
            $table->date('tanggal_transfer');

            $table->foreignId('acc_by')->nullable();  // NULL = belum diverifikasi
            $table->timestamp('acc_at')->nullable();  // timestamp verifikasi
            $table->text('rejection_note')->nullable(); // catatan penolakan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('pembayaran_cicilans');
    }
};
