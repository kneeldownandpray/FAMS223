<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_requirements', function (Blueprint $table) {
            $table->text('note')->nullable()->after('original_name');
            $table->enum('status', ['processing', 'accepted', 'rejected'])->default('processing')->after('note');
        });
    }
    
    public function down(): void
    {
        Schema::table('user_requirements', function (Blueprint $table) {
            $table->dropColumn(['note', 'status']);
        });
    }
    
};
