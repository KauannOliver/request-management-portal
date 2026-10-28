<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Etapa;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Etapa::firstOrCreate(
            ['nome' => 'Concluído'],
            ['ordem' => 6, 'ativo' => true]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Etapa::where('nome', 'Concluído')->delete();
    }
};
