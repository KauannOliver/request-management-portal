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
        Schema::table('solicitacoes', function (Blueprint $table) {
            $table->index('deleted_at', 'idx_solicitacoes_deleted_at');
            $table->index('etapa_id', 'idx_solicitacoes_etapa_id');
            $table->index('data_prazo', 'idx_solicitacoes_data_prazo');
        });

        Schema::table('logs_auditoria', function (Blueprint $table) {
            $table->index(['entidade', 'entidade_id'], 'idx_logs_auditoria_entidade');
            $table->index('created_at', 'idx_logs_auditoria_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitacoes', function (Blueprint $table) {
            $table->dropIndex('idx_solicitacoes_deleted_at');
            $table->dropIndex('idx_solicitacoes_etapa_id');
            $table->dropIndex('idx_solicitacoes_data_prazo');
        });

        Schema::table('logs_auditoria', function (Blueprint $table) {
            $table->dropIndex('idx_logs_auditoria_entidade');
            $table->dropIndex('idx_logs_auditoria_created_at');
        });
    }
};
