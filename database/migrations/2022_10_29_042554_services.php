<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Services extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

        public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('client_id');
//            $table->unsignedBigInteger('bill_id');
            $table->string('name');
            $table->boolean('price');
            $table->string('description');
            $table->timestamps();

//            $table->foreign('client_id')
//                ->references('id')->on('clients')
//                ->onDelete('cascade');
//            $table->foreign('bill_id')
//                ->references('id')->on('bills')  ->onDelete('cascade');

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
