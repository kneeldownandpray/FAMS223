<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('vehicle_records', function (Blueprint $table) {
            $table->boolean('vehicle_status')->default(true)->after('image');
        });
    }

    public function down(): void {
        Schema::table('vehicle_records', function (Blueprint $table) {
            $table->dropColumn('vehicle_status');
        });
    }
};
