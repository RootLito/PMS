<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('raw_calculations', function (Blueprint $table) {
            $table->string('cutoff')->nullable()->after('office_name');
        });
    }

    public function down(): void
    {
        Schema::table('raw_calculations', function (Blueprint $table) {
            if (Schema::hasColumn('raw_calculations', 'cutoff')) {
                $table->dropColumn('cutoff');
            }
        });
    }

};
