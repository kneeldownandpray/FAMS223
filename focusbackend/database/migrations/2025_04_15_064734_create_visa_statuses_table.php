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
        Schema::create('visa_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // one-to-one
            $table->boolean('application_received')->default(0);
            $table->boolean('interview_employer_confirmation')->default(0);
            $table->boolean('requirements')->default(0);
            $table->boolean('skill_assessment')->default(0);
            $table->boolean('visa_preparation')->default(0);
            $table->boolean('visa_lodged')->default(0);
            $table->boolean('medical_biometrics')->default(0);
            $table->boolean('awaiting_decision')->default(0);
            $table->boolean('visa_outcome')->default(0);
            $table->boolean('ready_to_fly')->default(0);
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_statuses');
    }
};
