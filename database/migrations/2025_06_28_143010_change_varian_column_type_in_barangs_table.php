<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVarianColumnTypeInBarangsTable extends Migration
{
    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->longText('varian')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('varian', 255)->nullable()->change(); // jika sebelumnya varchar
        });
    }
}
