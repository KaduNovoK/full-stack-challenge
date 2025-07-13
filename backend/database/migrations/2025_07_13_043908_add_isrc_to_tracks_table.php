<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsrcToTracksTable extends Migration
{
    public function up()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->string('isrc')->unique()->after('id');
        });
    }

    public function down()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropColumn('isrc');
        });
    }
}
