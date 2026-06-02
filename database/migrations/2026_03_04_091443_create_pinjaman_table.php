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
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('no_pinjaman')->unique();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('bunga', 5, 2); // persen
            $table->integer('tenor'); // bulan
            $table->decimal('monthly_payment', 15, 2);
            $table->decimal('total_payment', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'disbursed',
                'completed'
            ])->default('pending');

            $table->date('applied_date');
            $table->date('approved_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('disbursed_date')->nullable();
            $table->date('completed_date')->nullable();

            $table->timestamps();

            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
