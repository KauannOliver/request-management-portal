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
        Schema::create('solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->string('tipo'); // SAU, SD
            $table->text('descricao');
            $table->string('nome_solicitante'); // Nome do solicitante (sem perfil do sistema)
            $table->foreignId('etapa_id')->constrained('etapas');
            $table->integer('percentual_conclusao');
            $table->timestamp('data_abertura');
            $table->timestamp('data_prazo');
            $table->timestamp('data_entrega')->nullable();
            
            // Auditoria de autoria
            $table->foreignId('criado_por_id')->constrained('usuarios');
            $table->foreignId('atualizado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('excluido_por_id')->nullable()->constrained('usuarios');
            $table->text('motivo_exclusao')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            // Restrição de unicidade composta entre tipo e número
            $table->unique(['tipo', 'numero']);

            // Índices específicos
            $table->index('tipo');
            $table->index('etapa_id');
            $table->index('percentual_conclusao');
            $table->index('data_prazo');
            $table->index('data_abertura');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes');
    }
};
