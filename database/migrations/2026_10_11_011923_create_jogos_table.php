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
        Schema::create('jogos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('match_id')->unique();
            $table->integer('rodada');
            $table->string('adversario');
            $table->string('adversario_escudo')->nullable();
            $table->enum('mando', ['CASA', 'FORA']);
            $table->integer('gols_pro_1t')->nullable();
            $table->integer('gols_contra_1t')->nullable();
            $table->integer('gols_pro_total')->nullable();
            $table->integer('gols_contra_total')->nullable();
            $table->enum('resultado', ['V', 'E', 'D'])->nullable();
            $table->string('status');
            $table->dateTime('data_jogo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jogos');
    }
};
