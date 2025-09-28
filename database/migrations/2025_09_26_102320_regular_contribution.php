<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regular_contributions', function (Blueprint $table) {
            $table->id();

            $table->decimal('tax', 12, 2)->nullable();
            $table->decimal('phic', 12, 2)->nullable();
            $table->decimal('gsis_ps', 12, 2)->nullable();
            $table->decimal('hdmf_ps', 12, 2)->nullable();
            $table->decimal('hdmf_mp2', 12, 2)->nullable();
            $table->decimal('hdmf_mpl', 12, 2)->nullable();
            $table->decimal('hdmf_hl', 12, 2)->nullable();
            $table->decimal('gsis_pol', 12, 2)->nullable();
            $table->decimal('gsis_consoloan', 12, 2)->nullable();
            $table->decimal('gsis_emer', 12, 2)->nullable();
            $table->decimal('gsis_cpl', 12, 2)->nullable();
            $table->decimal('gsis_gfal', 12, 2)->nullable();
            $table->decimal('g_mpl', 12, 2)->nullable();
            $table->decimal('g_lite', 12, 2)->nullable();
            $table->decimal('bfar_provident', 12, 2)->nullable();
            $table->decimal('dareco', 12, 2)->nullable();
            $table->decimal('ucpb_savings', 12, 2)->nullable();
            $table->decimal('isda_savings_loan', 12, 2)->nullable();
            $table->decimal('isda_savings_cap_con', 12, 2)->nullable();
            $table->decimal('tagumcoop_sl', 12, 2)->nullable();
            $table->decimal('tagum_coop_cl', 12, 2)->nullable();
            $table->decimal('tagum_coop_sc', 12, 2)->nullable();
            $table->decimal('tagum_coop_rs', 12, 2)->nullable();
            $table->decimal('tagum_coop_ers_gasaka_suretech_etc', 12, 2)->nullable();
            $table->decimal('nd', 12, 2)->nullable();
            $table->decimal('lbp_sl', 12, 2)->nullable();
            $table->decimal('total_charges', 12, 2)->nullable();
            $table->decimal('total_salary', 12, 2)->nullable();
            $table->decimal('pera', 12, 2)->nullable();
            $table->decimal('gross', 12, 2)->nullable();
            $table->decimal('rate_per_month', 12, 2)->nullable();
            $table->decimal('leave_wo', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regular_contributions');
    }
};
