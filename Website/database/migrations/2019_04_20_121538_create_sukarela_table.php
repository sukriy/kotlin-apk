<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSukarelaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sukarela', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_user');
            $table->integer('nominal');
            $table->string('keterangan');

            $table->date('tgl_acc_admin');
            $table->bigInteger('id_admin');
            $table->date('acc_admin');

            $table->char('flag', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('sukarela');
    }
}
