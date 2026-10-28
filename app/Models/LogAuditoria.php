<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAuditoria extends Model
{
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'logs_auditoria';

    /**
     * Indica se o modelo deve ser marcado com timestamps automáticos.
     * Nós gerenciaremos apenas o created_at via banco, desativamos updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'acao',
        'entidade',
        'entidade_id',
        'descricao',
        'dados_anteriores',
        'dados_novos',
        'endereco_ip',
        'user_agent',
        'created_at',
    ];

    /**
     * Os atributos que devem ser convertidos em tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dados_anteriores' => 'array',
            'dados_novos' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com o Usuário que realizou a ação.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
