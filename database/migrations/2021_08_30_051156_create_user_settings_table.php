<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->float('tax')->nullable();
            $table->float('exterior_doors_price_per_square_footage')->nullable();
            $table->float('interior_doors_price_per_square_footage')->nullable();
            $table->float('windows_price_per_square_footage')->nullable();
            $table->float('gate_price_per_square_footage')->nullable();
            $table->float('exterior_doors_installation_price')->nullable();
            $table->float('interior_doors_installation_price')->nullable();
            $table->float('windows_installation_price')->nullable();
            $table->float('gate_installation_price')->nullable();
            $table->text('note_to_customer')->nullable();
            $table->string('cc')->nullable();
            $table->text('terms_and_condition')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
