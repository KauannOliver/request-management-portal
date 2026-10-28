<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Solicitacao extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'solicitacoes';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero',
        'tipo',
        'descricao',
        'observacoes',
        'nome_solicitante',
        'etapa_id',
        'percentual_conclusao',
        'data_abertura',
        'data_prazo',
        'data_entrega',
        'criado_por_id',
        'atualizado_por_id',
        'excluido_por_id',
        'motivo_exclusao',
    ];

    /**
     * Os atributos que devem ser convertidos em tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'percentual_conclusao' => 'integer',
            'data_abertura' => 'datetime',
            'data_prazo' => 'datetime',
            'data_entrega' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Inicialização e hooks do ciclo de vida do modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Solicitacao $solicitacao) {
            // Validações de data de prazo e abertura
            if ($solicitacao->data_prazo && $solicitacao->data_abertura) {
                if ($solicitacao->data_prazo->lt($solicitacao->data_abertura)) {
                    throw new \InvalidArgumentException('A data de prazo não pode ser anterior à data de abertura.');
                }
            }

            // Lógica de preenchimento automático de data de entrega
            if ($solicitacao->percentual_conclusao === 100) {
                if (!$solicitacao->isDirty('data_entrega') && is_null($solicitacao->data_entrega)) {
                    $solicitacao->data_entrega = now();
                }
                
                // Garantir que data de entrega não seja anterior à abertura
                if ($solicitacao->data_entrega && $solicitacao->data_abertura) {
                    if ($solicitacao->data_entrega->lt($solicitacao->data_abertura)) {
                        throw new \InvalidArgumentException('A data de entrega não pode ser anterior à data de abertura.');
                    }
                }
            } else {
                // Caso volte para menos de 100%, limpa a data de entrega
                $solicitacao->data_entrega = null;
            }
        });
    }

    /**
     * Relacionamento com a Etapa.
     */
    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class, 'etapa_id');
    }

    /**
     * Relacionamento muitos-para-muitos com Recursos (Usuários).
     */
    public function recursos(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'solicitacao_recursos', 'solicitacao_id', 'usuario_id')
                    ->withPivot('atribuido_por_id', 'atribuido_em');
    }

    /**
     * Usuário que criou o registro.
     */
    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por_id');
    }

    /**
     * Usuário que atualizou o registro por último.
     */
    public function atualizadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'atualizado_por_id');
    }

    /**
     * Usuário que realizou a exclusão lógica.
     */
    public function excluidoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'excluido_por_id');
    }

    /**
     * Verifica se a solicitação está atrasada.
     */
    public function getIsAtrasadaAttribute(): bool
    {
        if ($this->percentual_conclusao >= 100) {
            return false;
        }

        return $this->data_prazo && $this->data_prazo->isPast();
    }

    /**
     * Retorna o identificador formatado (ex: SAU 1025, SD 400).
     */
    public function getIdentificacaoAttribute(): string
    {
        return "{$this->tipo} {$this->numero}";
    }

    /**
     * Retorna a situação calculada.
     */
    public function getSituacaoCalculadaAttribute(): string
    {
        if ($this->is_atrasada) {
            return 'Atrasado';
        }

        if ($this->percentual_conclusao === 100) {
            return 'Concluído';
        }

        if ($this->percentual_conclusao === 0) {
            return 'Não iniciado';
        }

        return 'Em andamento';
    }

    /**
     * Retorna a classe CSS de cor para badge baseada na situação.
     */
    public function getSituacaoBadgeClassAttribute(): string
    {
        return match ($this->situacao_calculada) {
            'Atrasado' => 'badge-atrasado', // Laranja
            'Concluído' => 'badge-concluido', // Azul principal / Verde
            'Não iniciado' => 'badge-nao-iniciado', // Cinza
            'Em andamento' => 'badge-em-andamento', // Azul marinho
            default => 'badge-gray'
        };
    }

    /**
     * Retorna mensagem associada ao progresso do slider.
     */
    public function getMensagemProgressoAttribute(): string
    {
        if ($this->percentual_conclusao === 0) {
            return 'Planejamento e inicialização.';
        }
        if ($this->percentual_conclusao > 0 && $this->percentual_conclusao <= 25) {
            // Levantamento das necessidades iniciais
            return 'Levantamento e definição do escopo inicial.';
        }
        if ($this->percentual_conclusao > 25 && $this->percentual_conclusao <= 50) {
            return 'Desenvolvimento e execução em andamento.';
        }
        if ($this->percentual_conclusao > 50 && $this->percentual_conclusao <= 75) {
            return 'Refinamentos de código e arquitetura finalizados.';
        }
        if ($this->percentual_conclusao > 75 && $this->percentual_conclusao < 100) {
            return 'Homologação e testes finais com usuários.';
        }
        return 'Conclusão e implantação no ambiente de produção!';
    }

    /**
     * Retorna o tempo restante ou decorrido formatado.
     */
    /**
     * Formata a diferença de tempo de maneira amigável em dias, horas e minutos.
     */
    private function formatarDiferencaTempo(\Carbon\Carbon $de, \Carbon\Carbon $para): string
    {
        $diff = $de->diff($para);
        $days = $diff->days;
        $hours = $diff->h;
        $minutes = $diff->i;

        $partes = [];
        if ($days > 0) {
            $partes[] = "{$days} dia" . ($days > 1 ? 's' : '');
        }
        if ($hours > 0) {
            $partes[] = "{$hours} hora" . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $partes[] = "{$minutes} minuto" . ($minutes > 1 ? 's' : '');
        }

        if (empty($partes)) {
            return "menos de 1 minuto";
        }

        if (count($partes) === 1) {
            return $partes[0];
        }

        $ultimo = array_pop($partes);
        return implode(', ', $partes) . ' e ' . $ultimo;
    }

    /**
     * Calcula e formata amigavelmente o tempo restante ou o atraso da solicitação.
     */
    public function getTempoRestanteTextoAttribute(): string
    {
        if ($this->percentual_conclusao === 100) {
            if ($this->data_entrega && $this->data_prazo) {
                if ($this->data_entrega->gt($this->data_prazo)) {
                    $tempo = $this->formatarDiferencaTempo($this->data_prazo, $this->data_entrega);
                    return "Entregue com atraso de {$tempo}";
                } else {
                    return "Entregue no prazo";
                }
            }
            return "Concluído";
        }

        if ($this->is_atrasada) {
            $tempo = $this->formatarDiferencaTempo($this->data_prazo, now());
            return "Atrasado há {$tempo}";
        }

        if ($this->data_prazo) {
            $tempo = $this->formatarDiferencaTempo(now(), $this->data_prazo);
            return "Restam {$tempo} para o prazo";
        }

        return "-";
    }
}
