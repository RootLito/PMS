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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->json('hdmf_pi')->nullable();
            $table->json('hdmf_mpl')->nullable();
            $table->json('hdmf_mp2')->nullable();
            $table->json('hdmf_cl')->nullable();
            $table->json('dareco')->nullable();
            $table->json('sss')->nullable();
            $table->json('ec')->nullable();
            $table->json('wisp')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
