<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplyJobQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_job_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apply_job_id');
            $table->unsignedBigInteger('my_job_question_id');
            $table->text('answer')->nullable();

            $table->foreign('apply_job_id')->references('id')->on('apply_jobs')->onDelete('CASCADE')->onUpdate('NO ACTION');


            $table->foreign('my_job_question_id')->references('id')->on('my_job_questions')->onDelete('CASCADE')->onUpdate('NO ACTION');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apply_job_questions');
    }
}
