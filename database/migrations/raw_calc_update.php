<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('raw_calculations', function (Blueprint $table) {
            $table->integer('absent_ins')->nullable()->after('absent');
            $table->integer('late_ins')->nullable()->after('late_undertime');
            $table->text('remarks2')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('raw_calculations', function (Blueprint $table) {
            $table->dropColumn(['absent', 'late', 'remarks2']);
        });
    }
};
