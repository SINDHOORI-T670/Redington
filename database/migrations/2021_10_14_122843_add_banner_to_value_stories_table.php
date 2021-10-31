<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerToValueStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('value_stories', function (Blueprint $table) {
            $table->string('thumbnail')->default('http://redington.codeatechnologies.com/public/images/333 x 138.jpg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('value_stories', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
}
