<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('skill_assessment')->default(0); // 0 or 1
            $table->unsignedTinyInteger('visa_status')->default(0); // 0 to 10
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['skill_assessment', 'visa_status']);
        });
    }
    
    
};
