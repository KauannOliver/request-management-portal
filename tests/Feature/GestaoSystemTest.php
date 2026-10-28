<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Etapa;
use App\Models\Solicitacao;
use App\Models\LogAuditoria;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GestaoSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $gestor;
    protected $recurso1;
    protected $recurso2;
    protected $etapa1;
    protected $etapa2;

    /**
     * Configura o cenário de testes inicial.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Cadastrar as etapas obrigatórias
        $this->etapa1 = Etapa::firstOrCreate(['nome' => 'Ideia Registrada'], ['ordem' => 1, 'ativo' => true]);
        $this->etapa2 = Etapa::firstOrCreate(['nome' => 'Levantamento'], ['ordem' => 2, 'ativo' => true]);
        Etapa::firstOrCreate(['nome' => 'Processo de Software'], ['ordem' => 3, 'ativo' => true]);
        Etapa::firstOrCreate(['nome' => 'Homologação'], ['ordem' => 4, 'ativo' => true]);
        Etapa::firstOrCreate(['nome' => 'Aplicação'], ['ordem' => 5, 'ativo' => true]);
        Etapa::firstOrCreate(['nome' => 'Concluído'], ['ordem' => 6, 'ativo' => true]);

        // 2. Criar os usuários de teste
        $this->recurso1 = Usuario::create([
            'nome' => 'Recurso Um',
            'email' => 'recurso1@test.com',
            'password' => Hash::make('Password123!'),
            'perfil' => 'recurso',
            'ativo' => true,
        ]);

        $this->recurso2 = Usuario::create([
            'nome' => 'Recurso Dois',
            'email' => 'recurso2@test.com',
            'password' => Hash::make('Password123!'),
            'perfil' => 'recurso',
            'ativo' => true,
        ]);

        $this->gestor = Usuario::create([
            'nome' => 'Gestor Um',
            'email' => 'gestor1@test.com',
            'password' => Hash::make('Password123!'),
            'perfil' => 'gestor',
            'ativo' => true,
        ]);
    }

    /**
     * 1. Teste de login com usuário ativo.
     */
    public function test_login_com_usuario_ativo()
    {
        $response = $this->post('/login', [
            'email' => 'recurso1@test.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals($this->recurso1->id, Auth::id());
    }

    /**
     * 2. Teste de bloqueio de login para usuário inativo.
     */
    public function test_bloqueio_de_usuario_inativo()
    {
        $inativo = Usuario::create([
            'nome' => 'Inativo Um',
            'email' => 'inativo@test.com',
            'password' => Hash::make('Password123!'),
            'perfil' => 'recurso',
            'ativo' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inativo@test.com',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /**
     * 3. Teste de gestor acessando dashboard.
     */
    public function test_gestor_acessando_dashboard()
    {
        $this->actingAs($this->gestor);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    /**
     * 4. Teste de gestor visualizando solicitações.
     */
    public function test_gestor_visualizando_solicitacoes()
    {
        $this->actingAs($this->gestor);

        $solicitacao = Solicitacao::create([
            'numero' => '1025',
            'tipo' => 'SAU',
            'nome_solicitante' => 'Solicitante Teste',
            'descricao' => 'Descrição Teste',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $responseList = $this->get('/solicitacoes');
        $responseList->assertStatus(200);
        $responseList->assertSee('SAU 1025');

        $responseShow = $this->get("/solicitacoes/detalhes/{$solicitacao->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Descrição Teste');
    }

    /**
     * 5. Teste de gestor impedido de cadastrar.
     */
    public function test_gestor_sendo_impedido_de_cadastrar()
    {
        $this->actingAs($this->gestor);

        $responseGet = $this->get('/solicitacoes/criar');
        $responseGet->assertStatus(403);

        $responsePost = $this->post('/solicitacoes/criar', [
            'numero' => '1001',
            'tipo' => 'SAU',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 0,
            'data_abertura' => now()->format('Y-m-d H:i:s'),
            'data_prazo' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'recursos' => [$this->recurso1->id]
        ]);
        $responsePost->assertStatus(403);
    }

    /**
     * 6. Teste de gestor impedido de editar.
     */
    public function test_gestor_sendo_impedido_de_editar()
    {
        $this->actingAs($this->gestor);

        $solicitacao = Solicitacao::create([
            'numero' => '1026',
            'tipo' => 'SAU',
            'nome_solicitante' => 'Solicitante Teste',
            'descricao' => 'Descrição Teste',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $responseGet = $this->get("/solicitacoes/editar/{$solicitacao->id}");
        $responseGet->assertStatus(403);

        $responsePut = $this->put("/solicitacoes/editar/{$solicitacao->id}", [
            'numero' => '1026',
            'tipo' => 'SAU',
            'nome_solicitante' => 'Atualizado',
            'descricao' => 'Atualizado',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 20,
            'data_abertura' => now()->format('Y-m-d H:i:s'),
            'data_prazo' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'recursos' => [$this->recurso1->id]
        ]);
        $responsePut->assertStatus(403);
    }

    /**
     * 7. Teste de gestor impedido de excluir.
     */
    public function test_gestor_sendo_impedido_de_excluir()
    {
        $this->actingAs($this->gestor);

        $solicitacao = Solicitacao::create([
            'numero' => '1027',
            'tipo' => 'SAU',
            'nome_solicitante' => 'Solicitante Teste',
            'descricao' => 'Descrição Teste',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $responseDelete = $this->delete("/solicitacoes/excluir/{$solicitacao->id}", [
            'motivo_exclusao' => 'Motivo de teste'
        ]);
        $responseDelete->assertStatus(403);
        $this->assertDatabaseHas('solicitacoes', ['id' => $solicitacao->id, 'deleted_at' => null]);
    }

    /**
     * 8. Teste de gestor impedido de acessar usuários.
     */
    public function test_gestor_sendo_impedido_de_acessar_usuarios()
    {
        $this->actingAs($this->gestor);

        $response = $this->get('/usuarios');
        $response->assertStatus(403);
    }

    /**
     * 9. Teste de gestor impedido de acessar logs de auditoria.
     */
    public function test_gestor_sendo_impedido_de_acessar_logs()
    {
        $this->actingAs($this->gestor);

        $response = $this->get('/auditoria');
        $response->assertStatus(403);
    }

    /**
     * 10 e 12. Teste de recurso cadastrando solicitação com múltiplos responsáveis.
     */
    public function test_recurso_cadastrando_solicitacao()
    {
        $this->actingAs($this->recurso1);

        $response = $this->post('/solicitacoes/criar', [
            'numero' => '2000',
            'tipo' => 'SD',
            'nome_solicitante' => 'Solicitante SD',
            'descricao' => 'Descrição detalhada do SD',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 15,
            'data_abertura' => now()->format('Y-m-d H:i:s'),
            'data_prazo' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'recursos' => [$this->recurso1->id, $this->recurso2->id]
        ]);

        $response->assertRedirect('/solicitacoes');
        
        $solicitacao = Solicitacao::where('numero', '2000')->first();
        $this->assertNotNull($solicitacao);
        $this->assertEquals(2, $solicitacao->recursos()->count());
    }

    /**
     * 11. Teste de recurso editando solicitação.
     */
    public function test_recurso_editando_solicitacao()
    {
        $this->actingAs($this->recurso1);

        $solicitacao = Solicitacao::create([
            'numero' => '2001',
            'tipo' => 'SD',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $response = $this->put("/solicitacoes/editar/{$solicitacao->id}", [
            'numero' => '2001',
            'tipo' => 'SD',
            'nome_solicitante' => 'Nome Alterado',
            'descricao' => 'Desc Alterada',
            'etapa_id' => $this->etapa2->id,
            'percentual_conclusao' => 50,
            'data_abertura' => now()->format('Y-m-d H:i:s'),
            'data_prazo' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'recursos' => [$this->recurso2->id] // altera o recurso
        ]);

        $response->assertRedirect("/solicitacoes/detalhes/{$solicitacao->id}");
        $this->assertEquals(50, $solicitacao->fresh()->percentual_conclusao);
        $this->assertEquals('Desc Alterada', $solicitacao->fresh()->descricao);
    }

    /**
     * 13. Prevenção de recursos duplicados.
     */
    public function test_prevencao_de_recursos_duplicados()
    {
        $solicitacao = Solicitacao::create([
            'numero' => '2002',
            'tipo' => 'SD',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        // Tenta associar duas vezes o mesmo recurso via DB
        $solicitacao->recursos()->attach($this->recurso1->id);
        
        try {
            $solicitacao->recursos()->attach($this->recurso1->id);
            $this->fail("Deveria ter lançado uma exceção de chave duplicada no SQLite");
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertTrue(true); // Exceção correta
        }
    }

    /**
     * 14. Validação do percentual de conclusão (exceção em nível de model/banco).
     */
    public function test_validacao_do_percentual_entre_0_e_100()
    {
        $this->actingAs($this->recurso1);

        // Tentativa de postar com percentual = 150
        $response = $this->post('/solicitacoes/criar', [
            'numero' => '2003',
            'tipo' => 'SD',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 150,
            'data_abertura' => now()->format('Y-m-d H:i:s'),
            'data_prazo' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'recursos' => [$this->recurso1->id]
        ]);

        $response->assertSessionHasErrors('percentual_conclusao');
    }

    /**
     * 15. Preenchimento automático da data de entrega ao atingir 100%.
     */
    public function test_preenchimento_da_entrega_ao_alcancar_100()
    {
        $solicitacao = Solicitacao::create([
            'numero' => '2004',
            'tipo' => 'SD',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 90,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $this->assertNull($solicitacao->data_entrega);

        // Atualiza para 100%
        $solicitacao->update(['percentual_conclusao' => 100]);

        $this->assertNotNull($solicitacao->fresh()->data_entrega);
    }

    /**
     * 16. Reabertura de solicitação (percentual < 100% limpa data de entrega).
     */
    public function test_reabertura_de_solicitacao()
    {
        $solicitacao = Solicitacao::create([
            'numero' => '2005',
            'tipo' => 'SD',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 100, // Vai preencher entrega automaticamente
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $this->assertNotNull($solicitacao->data_entrega);

        // Volta para 90% (reabertura)
        $solicitacao->update(['percentual_conclusao' => 90]);

        $this->assertNull($solicitacao->fresh()->data_entrega);
    }

    /**
     * 17. Validação de identificação composta única (tipo + numero).
     */
    public function test_identificacao_composta_unica()
    {
        Solicitacao::create([
            'numero' => '3000',
            'tipo' => 'SAU',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        // Permite ter SD 3000 (mesmo número, tipo diferente)
        Solicitacao::create([
            'numero' => '3000',
            'tipo' => 'SD',
            'nome_solicitante' => 'B',
            'descricao' => 'B',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        // Bloqueia duplicado de SAU 3000
        try {
            Solicitacao::create([
                'numero' => '3000',
                'tipo' => 'SAU',
                'nome_solicitante' => 'C',
                'descricao' => 'C',
                'etapa_id' => $this->etapa1->id,
                'percentual_conclusao' => 10,
                'data_abertura' => now(),
                'data_prazo' => now()->addDays(5),
                'criado_por_id' => $this->recurso1->id,
            ]);
            $this->fail("Deveria ter disparado erro de unicidade composta no SQLite.");
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * 18. Exclusão lógica com motivo.
     */
    public function test_exclusao_logica()
    {
        $this->actingAs($this->recurso1);

        $solicitacao = Solicitacao::create([
            'numero' => '4000',
            'tipo' => 'SAU',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $response = $this->delete("/solicitacoes/excluir/{$solicitacao->id}", [
            'motivo_exclusao' => 'Demanda cancelada pelo cliente'
        ]);

        $response->assertRedirect('/solicitacoes');
        
        $this->assertSoftDeleted('solicitacoes', [
            'id' => $solicitacao->id,
            'motivo_exclusao' => 'Demanda cancelada pelo cliente',
            'excluido_por_id' => $this->recurso1->id
        ]);
    }

    /**
     * 19. Restauração de registro excluído logicamente.
     */
    public function test_restauracao()
    {
        $this->actingAs($this->recurso1);

        $solicitacao = Solicitacao::create([
            'numero' => '4001',
            'tipo' => 'SAU',
            'nome_solicitante' => 'A',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        // Exclui
        $solicitacao->update([
            'excluido_por_id' => $this->recurso1->id,
            'motivo_exclusao' => 'Para teste'
        ]);
        $solicitacao->delete();

        // Restaura
        $response = $this->post("/solicitacoes/restaurar/{$solicitacao->id}");
        $response->assertRedirect('/excluidas');

        $this->assertDatabaseHas('solicitacoes', [
            'id' => $solicitacao->id,
            'deleted_at' => null,
            'excluido_por_id' => null,
            'motivo_exclusao' => null
        ]);
    }

    /**
     * 20. Criação dos logs de auditoria automáticos.
     */
    public function test_criacao_dos_logs_de_auditoria()
    {
        $this->actingAs($this->recurso1);

        // Reseta logs de setup
        LogAuditoria::truncate();

        // Ação 1: Criação (Cadastro de solicitação)
        $solicitacao = Solicitacao::create([
            'numero' => '5000',
            'tipo' => 'SAU',
            'nome_solicitante' => 'A',
            'descricao' => 'Descrição Original',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 10,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $this->assertDatabaseHas('logs_auditoria', [
            'acao' => 'Cadastro de solicitação',
            'entidade' => 'solicitacoes',
            'entidade_id' => $solicitacao->id
        ]);

        // Ação 2: Mudança de percentual
        $solicitacao->update(['percentual_conclusao' => 45]);

        $this->assertDatabaseHas('logs_auditoria', [
            'acao' => 'Mudança de percentual',
            'entidade' => 'solicitacoes',
            'entidade_id' => $solicitacao->id
        ]);
    }

    /**
     * 21. Teste de filtros principais do dashboard.
     */
    public function test_filtros_principais_do_dashboard()
    {
        $this->actingAs($this->gestor);

        // Cadastra duas demandas distintas
        $sau = Solicitacao::create([
            'numero' => '6000',
            'tipo' => 'SAU',
            'nome_solicitante' => 'Solicitante SAU',
            'descricao' => 'A',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 0,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        $sd = Solicitacao::create([
            'numero' => '7000',
            'tipo' => 'SD',
            'nome_solicitante' => 'Solicitante SD',
            'descricao' => 'B',
            'etapa_id' => $this->etapa2->id,
            'percentual_conclusao' => 100,
            'data_abertura' => now(),
            'data_prazo' => now()->addDays(5),
            'criado_por_id' => $this->recurso1->id,
        ]);

        // Filtro por tipo=SAU
        $responseSau = $this->get('/dashboard?tipo=SAU');
        $responseSau->assertStatus(200);
        // Os indicadores devem refletir apenas a demanda do filtro
        $responseSau->assertSee('Dashboard Executivo');

        // Filtro por tipo=SD e progresso=100%
        $responseSd = $this->get('/dashboard?tipo=SD&faixa_conclusao=100');
        $responseSd->assertStatus(200);
    }

    /**
     * 22. Teste de regras condicionais de prazo (obrigatório para SD, nulo para SAU) e observações.
     */
    public function test_regras_condicionais_de_prazo_e_observacoes()
    {
        $this->actingAs($this->recurso1);

        // 1. Tenta criar uma solicitação do tipo SD sem data_prazo (deve falhar)
        $responseSdFalha = $this->post('/solicitacoes/criar', [
            'numero' => '8000',
            'tipo' => 'SD',
            'nome_solicitante' => 'Solicitante A',
            'descricao' => 'Descrição do teste SD',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 0,
            'data_abertura' => now()->format('Y-m-d H:i:s'),
            'recursos' => [$this->recurso1->id]
        ]);
        $responseSdFalha->assertSessionHasErrors('data_prazo');

        // 2. Tenta criar uma solicitação do tipo SAU sem data_prazo (deve passar e salvar data_prazo como nulo)
        $responseSauSucesso = $this->post('/solicitacoes/criar', [
            'numero' => '8001',
            'tipo' => 'SAU',
            'nome_solicitante' => 'Solicitante B',
            'descricao' => 'Descrição do teste SAU',
            'observacoes' => 'Minha observação de teste',
            'etapa_id' => $this->etapa1->id,
            'percentual_conclusao' => 0,
            'data_abertura' => now()->format('Y-m-d H:i:s'),
            'recursos' => [$this->recurso1->id]
        ]);
        $responseSauSucesso->assertRedirect('/solicitacoes');

        $solSau = Solicitacao::where('numero', '8001')->first();
        $this->assertNotNull($solSau);
        $this->assertNull($solSau->data_prazo);
        $this->assertEquals('Minha observação de teste', $solSau->observacoes);
    }
}
