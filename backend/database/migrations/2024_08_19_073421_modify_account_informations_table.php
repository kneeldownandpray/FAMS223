<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAccountInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_informations', function (Blueprint $table) {
            // Remove the foreign key constraint
            $table->dropForeign(['account_acceptor_id']);
            
            // Modify the column to be just an integer without foreign key constraints
            $table->unsignedBigInteger('account_acceptor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_informations', function (Blueprint $table) {
            // Revert the column to be a foreign key (optional)
            $table->dropColumn('account_acceptor_id');
            $table->foreignId('account_acceptor_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }
}
