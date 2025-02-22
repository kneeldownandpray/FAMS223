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
    Schema::table('users', function (Blueprint $table) {
        $table->integer('account_type')->default(0); // or specify a default value
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('account_type');
    });
}

};
