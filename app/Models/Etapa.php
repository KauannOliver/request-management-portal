<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etapa extends Model
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'etapas';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'ordem',
        'ativo',
    ];

    /**
     * Os atributos que devem ser convertidos em tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    /**
     * Relacionamento com as Solicitações nesta etapa.
     */
    public function solicitacoes(): HasMany
    {
        return $this->hasMany(Solicitacao::class, 'etapa_id');
    }
}
