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
        Schema::create('setor_simpanan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('simpanan_type', ['pokok', 'wajib', 'sukarela']);
            
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'disbursed',
                'completed'
            ])->default('pending');

            $table->decimal('amount', 15, 2);
            $table->string('bukti_setor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setor_simpanan_histories');
    }
};
