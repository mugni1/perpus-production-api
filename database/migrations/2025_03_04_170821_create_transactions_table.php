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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('borrowing_id');
            $table->enum('transaction_type', ['peminjaman', 'pengembalian', 'denda']);
            $table->integer('amount');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('borrowing_id')->references('id')->on('borrowings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};