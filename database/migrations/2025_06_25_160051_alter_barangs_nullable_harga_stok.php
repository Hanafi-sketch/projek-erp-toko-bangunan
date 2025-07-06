<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('harga')->nullable()->change();
            $table->integer('stok')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('harga')->change(); // balik ke NOT NULL jika sebelumnya
            $table->integer('stok')->change();
        });
    }
};
