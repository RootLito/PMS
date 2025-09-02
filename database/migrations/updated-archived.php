<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archived', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->after('cutoff');
            $table->unsignedSmallInteger('year')->after('month');
        });
    }

    public function down(): void
    {
        Schema::table('archived', function (Blueprint $table) {
            $table->dropColumn(['month', 'year']);
        });
    }
};
