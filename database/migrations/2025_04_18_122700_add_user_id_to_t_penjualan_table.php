<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('t_penjualan', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->after('pembeli');

        // Kalau pakai foreign key:
        $table->foreign('user_id')->references('user_id')->on('m_user')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('t_penjualan', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}

};
