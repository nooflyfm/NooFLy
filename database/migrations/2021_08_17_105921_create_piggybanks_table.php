<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiggybanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piggybanks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->double('balance', 11, 2);
            $table->integer('currency');
            $table->double('target_summ', 11, 2);
            $table->date('target_date');
            $table->integer('active');
            $table->integer('user_id');
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
        Schema::dropIfExists('piggybanks');
    }
}
