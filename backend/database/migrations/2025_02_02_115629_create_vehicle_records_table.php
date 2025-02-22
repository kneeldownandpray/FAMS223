<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('pattern'); // License plate pattern
            $table->string('color'); // Vehicle color
            $table->string('vehicle_type'); // Type of vehicle
            $table->longText('image')->nullable(); // Store the base64 image data in LONGTEXT
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    public function down(): void {
        Schema::dropIfExists('vehicle_records');
    }
};
