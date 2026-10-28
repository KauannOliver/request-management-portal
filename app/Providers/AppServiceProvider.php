<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Usuario;
use App\Models\Solicitacao;
use App\Observers\UsuarioObserver;
use App\Observers\SolicitacaoObserver;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Usuario::observe(UsuarioObserver::class);
        Solicitacao::observe(SolicitacaoObserver::class);
        Paginator::useBootstrapFive();
    }
}
