<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announces', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->mediumText('description');
            $table->enum('dealtype',['rent','sale','exchange']);
            $table->enum('propretytype',['appartement','villa',
                                                'carcass','building','studio','land','barn','bungalow','others']);
            $table->integer('roomnumber');
            $table->integer('surface');
            $table->integer('price');
            $table->string('img');
            $table->integer('viewsnumber')->default(0);
            $table->json('place');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('announces');
    }
}
