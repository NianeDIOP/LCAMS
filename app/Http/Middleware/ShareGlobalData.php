<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Etablissement;
use Illuminate\Support\Facades\View;

class ShareGlobalData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Récupérer les informations de l'établissement
        $etablissement = Etablissement::first();
        
        // Partager les données avec toutes les vues
        View::share('etablissementGlobal', $etablissement);
        
        return $next($request);
    }
}