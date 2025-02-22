<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->string('contact_no')->nullable(); // Add contact_no column
            $table->string('profession')->nullable(); // Add profession column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn('contact_no'); // Drop contact_no column
            $table->dropColumn('profession'); // Drop profession column
        });
    }
};
