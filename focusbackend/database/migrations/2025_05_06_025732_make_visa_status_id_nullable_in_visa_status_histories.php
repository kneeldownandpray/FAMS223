<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visa_status_histories', function (Blueprint $table) {
            // Baguhin ang visa_status_id upang maging nullable
            $table->unsignedBigInteger('visa_status_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('visa_status_histories', function (Blueprint $table) {
            // Ibalik ang visa_status_id sa hindi nullable
            $table->unsignedBigInteger('visa_status_id')->nullable(false)->change();
        });
    }
};
