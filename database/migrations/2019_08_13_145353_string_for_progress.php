<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StringForProgress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->string('last', 255)->default('0')->change();
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->string('value', 255)->default('0')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->decimal('last', 7, 3)->change();
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->decimal('value', 7, 3)->change();
        });
    }
}
