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
        Schema::table('requirement_types', function (Blueprint $table) {
            $table->unsignedBigInteger('inserted_by_id')->nullable(); // Add the inserted_by_id column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requirement_types', function (Blueprint $table) {
            $table->dropColumn('inserted_by_id'); // Remove the column if the migration is rolled back
        });
    }
};
