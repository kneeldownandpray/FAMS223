<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_informations', function (Blueprint $table) {
            $table->id(); // Automatic ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key for user
            $table->boolean('requirements')->default(0); // Requirements (0 for now, future 1)
            $table->unsignedTinyInteger('account_status')->default(1); // Account status (1, 2, 3)
            $table->foreignId('account_acceptor_id')->nullable()->constrained('users')->onDelete('set null'); // User who accepted the account
            $table->boolean('deleted')->default(0); // Deletion (0 or 1)
            $table->string('company')->nullable(); // Company information
            $table->unsignedTinyInteger('skills_assessment')->nullable(); // Skills assessment (1 or 2)
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_informations');
    }
}
