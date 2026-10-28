<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Actions\Usuario\CreateUsuarioAction;
use App\Actions\Usuario\UpdateUsuarioAction;
use App\Actions\Usuario\RedefinirSenhaAction;

class UsuarioController extends Controller
{
    /**
     * Inicializa a autorização de forma centralizada.
     */
    public function __construct()
    {
        // Nota: O middleware 'perfil:recurso' protegerá todas as rotas deste controller na definição de rotas.
    }

    /**
     * Exibe a listagem dos usuários.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Usuario::class);

        $query = Usuario::query();

        // Filtro de Busca livre por nome ou e-mail
        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%");
            });
        }

        // Filtro por perfil
        if ($request->filled('perfil')) {
            $query->where('perfil', $request->input('perfil'));
        }

        // Filtro por ativo/inativo
        if ($request->filled('ativo')) {
            $query->where('ativo', $request->input('ativo') == 1);
        }

        // Ordenação
        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['nome', 'email', 'perfil', 'ativo', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'asc';
            }
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('nome', 'asc');
        }

        // Paginação dinâmica (10, 25 ou 40 itens)
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 40])) {
            $perPage = 10;
        }
        $usuarios = $query->paginate($perPage)->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Exibe o formulário de cadastro.
     */
    public function create()
    {
        Gate::authorize('create', Usuario::class);
        return view('usuarios.create');
    }

    /**
     * Armazena um novo usuário.
     */
    public function store(StoreUsuarioRequest $request, CreateUsuarioAction $action)
    {
        Gate::authorize('create', Usuario::class);
 
        $action->execute($request->validated());
 
        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        Gate::authorize('update', $usuario);

        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Atualiza os dados do usuário.
     */
    public function update(UpdateUsuarioRequest $request, $id, UpdateUsuarioAction $action)
    {
        $usuario = Usuario::findOrFail($id);
        Gate::authorize('update', $usuario);

        // Regra Crítica: Não permitir desativar ou rebaixar o perfil do último Recurso ativo do sistema
        $foiDesativado = $usuario->ativo && !$request->input('ativo');
        $foiRebaixado = $usuario->isRecurso() && $request->input('perfil') !== 'recurso';

        if ($foiDesativado || $foiRebaixado) {
            $totalRecursosAtivos = Usuario::recurso()->ativo()->count();
            if ($totalRecursosAtivos <= 1) {
                return back()->withErrors([
                    'ativo' => 'Impossível alterar. Este usuário é o único Recurso ativo do sistema e deve permanecer ativo e com perfil de Recurso.'
                ])->withInput();
            }
        }

        $action->execute($usuario, $request->validated());

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Redefine de forma segura a senha de um usuário.
     */
    public function redefinirSenha(Request $request, $id, RedefinirSenhaAction $action)
    {
        $usuario = Usuario::findOrFail($id);
        Gate::authorize('update', $usuario);

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]
        ]);

        $action->execute($usuario, $request->input('password'));

        // Registrar auditoria específica de redefinição de senha
        AuditoriaService::registrar(
            'Alteração de usuário',
            'usuarios',
            $usuario->id,
            "A senha do usuário {$usuario->nome} ({$usuario->email}) foi redefinida por um recurso."
        );

        return redirect()->route('usuarios.edit', $usuario->id)->with('success', 'Senha redefinida com sucesso!');
    }

    /**
     * Exibe a listagem de sessões ativas no banco de dados.
     */
    public function sessoes(Request $request)
    {
        Gate::authorize('viewAny', Usuario::class);

        $query = DB::table('sessions')
            ->join('usuarios', 'sessions.user_id', '=', 'usuarios.id')
            ->select('sessions.*', 'usuarios.nome as user_nome', 'usuarios.email as user_email');

        // Ordenação
        $sort = $request->input('sort', 'last_activity');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = [
            'user_nome' => 'usuarios.nome',
            'ip_address' => 'sessions.ip_address',
            'last_activity' => 'sessions.last_activity'
        ];

        $sortField = $allowedSorts[$sort] ?? 'sessions.last_activity';
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query->orderBy($sortField, $direction);

        // Paginação dinâmica (10, 25 ou 40 itens)
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 40])) {
            $perPage = 10;
        }

        $sessoes = $query->paginate($perPage)->withQueryString();

        return view('usuarios.sessoes', compact('sessoes'));
    }

    /**
     * Revoga uma sessão ativa e força o logout do usuário correspondente.
     */
    public function revogarSessao($id)
    {
        Gate::authorize('create', Usuario::class);

        DB::table('sessions')->where('id', $id)->delete();

        return redirect()->route('usuarios.sessoes')->with('success', 'Sessão revogada com sucesso!');
    }
}
