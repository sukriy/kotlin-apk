<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinjamanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_user');
            $table->integer('nominal');
            $table->integer('tenor');
            $table->double('bunga', 8, 2);
            $table->integer('cicilan');
            $table->string('keterangan');

            $table->date('tgl_acc_admin');
            $table->bigInteger('id_admin');
            $table->date('acc_admin');

            $table->date('tgl_acc_ketua');
            $table->bigInteger('id_ketua');
            $table->date('acc_ketua');

            $table->char('flag', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pinjaman');
    }
}
