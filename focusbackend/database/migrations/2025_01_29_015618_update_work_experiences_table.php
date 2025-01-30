<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWorkExperiencesTable extends Migration
{
    public function up()
    {
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->dropColumn('job_description'); // Removing job_description
        });
    }

    public function down()
    {
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->text('job_description')->nullable(); // Add back if rollback
        });
    }
}
