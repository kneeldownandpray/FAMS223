<?php
// Migration file (for example, 2025_04_06_000000_add_camera_detail_to_vehicle_records_table.php)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCameraDetailToVehicleRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('vehicle_records', function (Blueprint $table) {
            $table->string('camera_detail')->nullable(); // Add the camera_detail column
        });
    }

    public function down()
    {
        Schema::table('vehicle_records', function (Blueprint $table) {
            $table->dropColumn('camera_detail');
        });
    }
}
