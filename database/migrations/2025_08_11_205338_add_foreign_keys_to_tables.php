<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('tbl_roles');
        });


    }

    public function down()
    {
        Schema::table('tbl_users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });

    }
};