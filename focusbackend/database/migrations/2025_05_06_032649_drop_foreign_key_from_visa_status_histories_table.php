<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visa_status_histories', function (Blueprint $table) {
            $table->dropForeign(['visa_status_id']);
        });
    }

    public function down(): void
    {
        Schema::table('visa_status_histories', function (Blueprint $table) {
            $table->foreign('visa_status_id')->references('id')->on('visa_statuses')->onDelete('cascade');
        });
    }
};
