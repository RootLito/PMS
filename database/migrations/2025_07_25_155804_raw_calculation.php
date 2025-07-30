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
        Schema::create('raw_calculations', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('employee_id');
            
            $table->decimal('absent', 8, 2)->nullable();
            $table->decimal('late_undertime', 8, 2)->nullable();
            $table->decimal('total_absent_late', 8, 2)->nullable();
            $table->decimal('net_late_absences', 8, 2)->nullable();
            $table->decimal('tax', 8, 2)->nullable();
            $table->decimal('net_tax', 8, 2)->nullable();
            $table->decimal('hdmf_pi', 8, 2)->nullable();
            $table->decimal('hdmf_mpl', 8, 2)->nullable();
            $table->decimal('hdmf_mp2', 8, 2)->nullable();
            $table->decimal('hdmf_cl', 8, 2)->nullable();
            $table->decimal('dareco', 8, 2)->nullable();
            $table->decimal('ss_con', 8, 2)->nullable();
            $table->decimal('ec_con', 8, 2)->nullable();
            $table->decimal('wisp', 8, 2)->nullable();
            $table->decimal('total_deduction', 10, 2)->nullable();
            $table->decimal('net_pay', 10, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->string('voucher_include')->nullable();


            $table->timestamps();

            // Foreign key constraint
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_calculations');
    }
};
