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
        Schema::create('r_pembayaran_', function (Blueprint $table) {
            $table->id();
            $table->integer('siswa_id');
            $table->date('tanggal_pembayaran');
            $table->double('jumlah_bayar');
            $table->string('status_pembayaran');
            $table->integer('keterlambatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_pembayaran_');
    }
};
