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
        Schema::create('solicitacao_recursos', function (Blueprint $table) {
            $table->foreignId('solicitacao_id')->constrained('solicitacoes')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('atribuido_por_id')->nullable()->constrained('usuarios');
            $table->timestamp('atribuido_em')->useCurrent();

            // Chave composta para impedir duplicidade
            $table->primary(['solicitacao_id', 'usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacao_recursos');
    }
};
