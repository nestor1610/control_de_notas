<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('condicion')->default(1);

            $table->integer('rol_id')->unsigned();
            $table->foreign('rol_id')->references('id')->on('roles');

            $table->rememberToken();
        });

        DB::table('users')->insert([
            'id' => 1,
            'email' => 'admin@admin.com.ve',
            'password' => bcrypt( 'admin' ),
            'condicion' => 1,
            'rol_id' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
