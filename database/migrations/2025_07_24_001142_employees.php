<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('last_name', 100);
            $table->string('first_name', 100);
            $table->string('middle_initial', 1)->nullable();
            $table->string('suffix', 20)->nullable();

            $table->string('designation');
            $table->string('office_name');
            $table->string('office_code');

            $table->string('employment_status');
            $table->decimal('monthly_rate', 7, 2);
            $table->decimal('gross', 7, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
