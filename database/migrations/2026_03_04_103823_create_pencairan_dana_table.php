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
        Schema::create('pencairan_dana', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pinjaman_id')
                ->constrained('pinjaman')
                ->onDelete('cascade');

            // Snapshot rekening saat pencairan
            $table->string('metode_transfer'); // DANA, BCA, OVO, dll
            $table->string('nama_pemilik');
            $table->string('nomor_rekening');

            $table->decimal('jumlah_transfer', 15, 2);
            $table->string('bukti_transfer');
            $table->date('tanggal_transfer');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairan_dana');
    }
};
