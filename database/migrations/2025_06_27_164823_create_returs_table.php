<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('returs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('pelanggan');
            $table->foreignId('barang_id')->constrained()->onDelete('cascade');
            $table->string('varian_nama')->nullable();
            $table->integer('jumlah');
            $table->integer('total_harga')->nullable();
            $table->text('alasan');
            $table->enum('tipe_retur', ['Tukar Barang', 'Pengembalian Uang']);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returs');
    }
};
