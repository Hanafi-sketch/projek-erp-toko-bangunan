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
        Schema::table('returs', function (Blueprint $table) {
            if (!Schema::hasColumn('returs', 'tipe_retur')) {
                $table->enum('tipe_retur', ['Tukar Barang', 'Pengembalian Uang'])->after('alasan');
            }

            if (!Schema::hasColumn('returs', 'barang_id')) {
                $table->foreignId('barang_id')->nullable()->after('pelanggan');
            }

            // Hapus baris ini karena kolom 'alasan' sudah ada
            // $table->text('alasan')->after('jumlah');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
