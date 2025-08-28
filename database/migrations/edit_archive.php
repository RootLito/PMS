<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('archived', function (Blueprint $table) {
            $table->string('cutoff')->change();
        });
    }

    public function down()
    {
        Schema::table('archived', function (Blueprint $table) {
            $table->date('cutoff')->change();
        });
    }
};
