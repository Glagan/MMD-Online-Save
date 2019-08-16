<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameChapterId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_titles', function (Blueprint $table) {
            $table->renameColumn('chapter_id', 'chapter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history_titles', function (Blueprint $table) {
            $table->renameColumn('chapter', 'chapter_id');
        });
    }
}
