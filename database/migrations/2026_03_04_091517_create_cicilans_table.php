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
        Schema::create('cicilans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pinjaman_id')
                  ->constrained('pinjaman')
                  ->onDelete('cascade');

            $table->integer('no_cicilan');
            $table->date('due_date');

            $table->decimal('pokok', 15, 2);
            $table->decimal('bunga', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['unpaid', 'pending_verification', 'rejected', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('paid_date')->nullable();
            $table->decimal('late_fee', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cicilans');
    }
};
