<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleAndDescriptionToUserVideosTable extends Migration
{
    public function up()
    {
        Schema::table('user_videos', function (Blueprint $table) {
            $table->string('title')->nullable(); // Add title column
            $table->text('description')->nullable(); // Add description column
        });
    }

    public function down()
    {
        Schema::table('user_videos', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }
}
