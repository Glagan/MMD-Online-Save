<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHistory extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('history_titles', function (Blueprint $table) {
			$table->unsignedBigInteger('lastRead')->default(0);
			$table->string('highest', 10)->default('0');
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
			$table->dropColumn('lastRead');
			$table->dropColumn('highest');
		});
	}
}
