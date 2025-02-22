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
        Schema::create('hired', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade'); // Link to Employer (users table)
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade'); // Link to Applicant (users table)
            $table->boolean('approval_of_admin')->default(false); // Admin approval status
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hired');
    }
    

    /**
     * Reverse the migrations.
     */

};
