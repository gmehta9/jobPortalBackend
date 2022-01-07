<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_jobs', function (Blueprint $table) {
            $table->id();


            $table->string('title');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('description');
            $table->string('type')->nullable();
            $table->string('profile')->nullable();
            $table->dateTime('expiry')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('NO ACTION');
            $table->softDeletes();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_jobs');
    }
}
