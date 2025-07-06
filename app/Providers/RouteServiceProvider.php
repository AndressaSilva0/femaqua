<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Caminho padrão para a rota inicial da aplicação (opcional).
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Bootstrap das rotas.
     */
    public function boot(): void
    {
        // Carrega as rotas da API (routes/api.php)
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        // Carrega as rotas da web (routes/web.php)
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}

