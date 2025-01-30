<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobDescriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('job_descriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_experience_id'); // Foreign key to work_experiences
            $table->text('description'); // Job description details
            $table->timestamps();

            $table->foreign('work_experience_id')->references('id')->on('work_experiences')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_descriptions');
    }
}
