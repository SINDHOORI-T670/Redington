<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerToValueJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('value_journals', function (Blueprint $table) {
            $table->string('thumbnail')->default('http://redington.codeatechnologies.com/public/images/102-37default.jpg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('value_journals', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
}
