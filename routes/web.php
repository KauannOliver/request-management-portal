<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MinhaContaController;
use App\Http\Controllers\SolicitacaoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LogAuditoriaController;
use App\Http\Controllers\PrazoController;
use App\Http\Controllers\KanbanController;

// Rota de Healthcheck mínima e pública (retorna {status: ok}) sem expor dados
Route::get('/healthcheck', function () {
    return response()->json(['status' => 'ok']);
})->name('healthcheck');

// Rotas de Autenticação (Apenas Convidados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.post');
});

// Rotas Autenticadas (Acessíveis por Gestores e Recursos)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Redirecionamento da raiz
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard Geral
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Visualização de Prazos
    Route::get('/prazos', [PrazoController::class, 'index'])->name('prazos.index');

    // Quadro Kanban
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::patch('/api/solicitacoes/{id}/etapa', [KanbanController::class, 'updateEtapa'])->name('solicitacoes.update-etapa');

    // Minha Conta
    Route::get('/minha-conta', [MinhaContaController::class, 'index'])->name('minha-conta.index');
    Route::put('/minha-conta', [MinhaContaController::class, 'update'])->name('minha-conta.update');

    // Solicitações (Visualização comum)
    Route::get('/solicitacoes', [SolicitacaoController::class, 'index'])->name('solicitacoes.index');
    Route::get('/solicitacoes/detalhes/{id}', [SolicitacaoController::class, 'show'])->name('solicitacoes.show');

    // ==========================================
    // Rotas Administrativas (Apenas Recursos)
    // ==========================================
    Route::middleware('perfil:recurso')->group(function () {
        // Solicitações (Ações de Escrita)
        Route::get('/solicitacoes/criar', [SolicitacaoController::class, 'create'])->name('solicitacoes.create');
        Route::post('/solicitacoes/criar', [SolicitacaoController::class, 'store'])->name('solicitacoes.store');
        Route::get('/solicitacoes/editar/{id}', [SolicitacaoController::class, 'edit'])->name('solicitacoes.edit');
        Route::put('/solicitacoes/editar/{id}', [SolicitacaoController::class, 'update'])->name('solicitacoes.update');
        Route::delete('/solicitacoes/excluir/{id}', [SolicitacaoController::class, 'destroy'])->name('solicitacoes.destroy');
        
        // Área de Excluídas e Restauração
        Route::get('/excluidas', [SolicitacaoController::class, 'lixeira'])->name('solicitacoes.lixeira');
        Route::post('/solicitacoes/restaurar/{id}', [SolicitacaoController::class, 'restore'])->name('solicitacoes.restore');

        // Gestão de Usuários
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/criar', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios/criar', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/editar/{id}', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/editar/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::put('/usuarios/{id}/redefinir-senha', [UsuarioController::class, 'redefinirSenha'])->name('usuarios.redefinir-senha');
        Route::get('/usuarios/sessoes', [UsuarioController::class, 'sessoes'])->name('usuarios.sessoes');
        Route::delete('/usuarios/sessoes/{id}', [UsuarioController::class, 'revogarSessao'])->name('usuarios.revogar-sessao');
        Route::post('/solicitacoes/lote/reatribuir', [SolicitacaoController::class, 'bulkReassign'])->name('solicitacoes.bulk-reassign');

        // Logs de Auditoria
        Route::get('/auditoria', [LogAuditoriaController::class, 'index'])->name('logs-auditoria.index');
        Route::get('/auditoria/{id}', [LogAuditoriaController::class, 'show'])->name('logs-auditoria.show');
        Route::post('/auditoria/limpar', [LogAuditoriaController::class, 'limparOldLogs'])->name('logs-auditoria.limpar');
    });
});
