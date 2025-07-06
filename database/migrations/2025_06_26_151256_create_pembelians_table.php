<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('supplier');
            $table->string('nama_barang');
            $table->string('gambar')->nullable();
            $table->integer('jumlah');
            $table->integer('total');
            $table->json('varians'); // Simpan varian sebagai JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
