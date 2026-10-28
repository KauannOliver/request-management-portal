<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditoriaService;

class CheckPerfil
{
    /**
     * Trata uma requisição de entrada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $perfil
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $perfil): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        // Se o perfil exigido for 'recurso' e o usuário logado for 'gestor', bloqueia
        if ($perfil === 'recurso' && !$usuario->isRecurso()) {
            // Auditoria da tentativa de invasão/acesso negado
            AuditoriaService::registrar(
                'Tentativa de acesso não autorizado',
                'seguranca',
                null,
                "O gestor {$usuario->nome} ({$usuario->email}) tentou acessar uma rota restrita para recursos: " . $request->getRequestUri()
            );

            abort(403, 'Acesso não autorizado. Esta página é restrita a usuários com perfil de Recurso.');
        }

        return $next($request);
    }
}
