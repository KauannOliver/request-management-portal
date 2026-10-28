<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'perfil',
        'ativo',
        'ultimo_acesso_em',
    ];

    /**
     * Os atributos que devem ser ocultados em arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos em tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'ativo' => 'boolean',
            'ultimo_acesso_em' => 'datetime',
        ];
    }

    /**
     * Helper para verificar se o usuário é Gestor.
     */
    public function isGestor(): bool
    {
        return $this->perfil === 'gestor';
    }

    /**
     * Helper para verificar se o usuário é Recurso.
     */
    public function isRecurso(): bool
    {
        return $this->perfil === 'recurso';
    }

    /**
     * Escopo para listar apenas usuários ativos.
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Escopo para listar apenas usuários com perfil Recurso.
     */
    public function scopeRecurso($query)
    {
        return $query->where('perfil', 'recurso');
    }

    /**
     * Relacionamento muitos-para-muitos com Solicitações em que é responsável.
     */
    public function solicitacoes(): BelongsToMany
    {
        return $this->belongsToMany(Solicitacao::class, 'solicitacao_recursos', 'usuario_id', 'solicitacao_id')
                    ->withPivot('atribuido_por_id', 'atribuido_em')
                    ->withTimestamps();
    }
}
