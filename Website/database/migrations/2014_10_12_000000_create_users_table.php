<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('namalengkap');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->integer('gaji')->nullable();
            $table->string('level')->default('Anggota');
            $table->string('api_token')->unique()->nullable();
            $table->string('gambar')->nullable();
            $table->date('tgl_join')->nullable();
            $table->date('tgl_resign')->nullable();
            $table->char('flag', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
