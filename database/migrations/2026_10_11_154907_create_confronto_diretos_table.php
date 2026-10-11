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
        Schema::create('confronto_diretos', function (Blueprint $table) {
            $table->id();
            $table->integer('match_id')->unique();
            $table->string('adversario_nome');
            $table->integer('total_jogos');
            $table->integer('vitorias');
            $table->integer('empates');
            $table->integer('derrotas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confronto_diretos');
    }
};
