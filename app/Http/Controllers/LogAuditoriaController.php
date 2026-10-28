<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAuditoria;
use App\Models\Usuario;

class LogAuditoriaController extends Controller
{
    /**
     * Exibe a listagem de auditoria com paginação e filtros.
     */
    public function index(Request $request)
    {
        $query = LogAuditoria::with('usuario');

        // Filtro por usuário
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->input('usuario_id'));
        }

        // Filtro por ação
        if ($request->filled('acao')) {
            $query->where('acao', $request->input('acao'));
        }

        // Filtro por entidade
        if ($request->filled('entidade')) {
            $query->where('entidade', $request->input('entidade'));
        }

        // Filtro por identificador da entidade
        if ($request->filled('entidade_id')) {
            $query->where('entidade_id', $request->input('entidade_id'));
        }

        // Filtro por período
        if ($request->filled('data_inicial')) {
            $query->whereDate('created_at', '>=', $request->input('data_inicial'));
        }
        if ($request->filled('data_final')) {
            $query->whereDate('created_at', '<=', $request->input('data_final'));
        }

        // Filtro por endereço IP
        if ($request->filled('endereco_ip')) {
            $query->where('endereco_ip', 'like', "%" . $request->input('endereco_ip') . "%");
        }

        // Filtro por texto livre (busca na descrição do log)
        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where('descricao', 'like', "%{$busca}%");
        }

        // Ordenação
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = ['created_at', 'acao', 'entidade', 'endereco_ip'];
        if (in_array($sort, $allowedSorts)) {
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'desc';
            }
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginação dinâmica (10, 25 ou 40 itens)
        $perPage = $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 40])) {
            $perPage = 10;
        }
        $logs = $query->paginate($perPage)->withQueryString();

        // Obter dados auxiliares para preencher os seletores dos filtros
        $usuarios = Usuario::orderBy('nome')->get();
        $acoesUnicas = LogAuditoria::select('acao')->distinct()->orderBy('acao')->pluck('acao');
        $entidadesUnicas = LogAuditoria::select('entidade')->distinct()->orderBy('entidade')->pluck('entidade');

        return view('logs-auditoria.index', compact('logs', 'usuarios', 'acoesUnicas', 'entidadesUnicas'));
    }

    /**
     * Exibe os detalhes de um log de auditoria individual.
     */
    public function show($id)
    {
        $log = LogAuditoria::with('usuario')->findOrFail($id);

        return view('logs-auditoria.show', compact('log'));
    }

    /**
     * Limpa os logs de auditoria deixando apenas as últimas 24 horas.
     */
    public function limparOldLogs(Request $request)
    {
        $limiar = now()->subHours(24);
        $deletados = LogAuditoria::where('created_at', '<', $limiar)->delete();

        // Registra o próprio evento de limpeza na auditoria
        \App\Services\AuditoriaService::registrar(
            'Exclusão lógica',
            'logs_auditoria',
            null,
            "Limpeza de logs retroativos a 24 horas executada. " . $deletados . " registros removidos."
        );

        return redirect()->route('logs-auditoria.index')->with('success', 'Logs com mais de 24h limpos com sucesso! (' . $deletados . ' registros removidos)');
    }
}
