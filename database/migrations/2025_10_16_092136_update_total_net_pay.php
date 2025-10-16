<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raw_calculations', function(Blueprint $table){
            $table->decimal('adjustment', 10, 2)->nullable()->after('net_pay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_calculations', function(Blueprint $table){
            $table->dropColumn('adjustment');
        });
    }
};
