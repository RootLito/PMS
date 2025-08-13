<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('raw_calculations', function (Blueprint $table) {
            $table->string('office_name')->nullable()->after('voucher_include');
            $table->string('office_code')->nullable()->after('office_name');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('raw_calculations', function (Blueprint $table) {
            $table->dropColumn(['office_name', 'office_code']);
        });
    }

};