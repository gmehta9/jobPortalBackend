<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->float('width_feet')->nullable();
            $table->float('width_inch')->nullable();
            $table->integer('width_fraction1')->nullable();
            $table->integer('width_fraction2')->nullable();
            $table->enum('type', ['doors','installation','others'])->default('doors');
            $table->float('height_feet')->nullable();
            $table->float('height_inch')->nullable();
            $table->integer('height_fraction1')->nullable();
            $table->integer('height_fraction2')->nullable();
            $table->text('description')->nullable();
            $table->string('quote_id')->nullable();
            $table->float('tax')->nullable();
            $table->boolean('is_taxable')->nullable();
            $table->float('price')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('discount')->nullable();
            $table->float('square_feet')->nullable();
            $table->float('total_price')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('NO ACTION');
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade')->onUpdate('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_items');
    }
}
