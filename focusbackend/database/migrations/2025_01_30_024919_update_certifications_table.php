<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCertificationsTable extends Migration
{
    public function up()
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->integer('year_received')->change(); // Change the column type to integer
        });
    }

    public function down()
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->year('year_received')->change(); // Rollback to the previous data type if needed
        });
    }
}
