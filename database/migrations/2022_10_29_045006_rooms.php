<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->string('name_room');
            $table->string('typ_room');
            $table->double('price');
            $table->integer('capacity');

            $table->string('description');
            $table->integer('status');
            $table->date('date');
            $table->timestamps();


            $table->foreign('bill_id')
                ->references('id')->on('bills')
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
