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
        Schema::create('regulars', function(Blueprint $table){
            $table->id();

            $table->string('last_name', 100);
            $table->string('first_name', 100);
            $table->string('middle_initial', 100)->nullable();
            $table->string('suffix', 20)->nullable();

            $table->string('office');
            $table->string('position');
            $table->decimal('monthly_rate', 7, 2);
            $table->decimal('gross', 7, 2);
            $table->string('gender');
            $table->string('sl_code', 10);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulars');
    }
};
