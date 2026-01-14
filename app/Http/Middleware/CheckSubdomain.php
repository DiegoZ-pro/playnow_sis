<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $subdomain = null): Response
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        $currentSubdomain = $parts[0] ?? null;

        // Validar que el subdominio actual coincida con el requerido
        if ($subdomain && $currentSubdomain !== $subdomain) {
            abort(403, 'Acceso no autorizado a este subdominio.');
        }

        // Validar que el subdominio esté en la lista de permitidos
        $allowedSubdomains = config('domain.allowed_subdomains', []);
        if (!in_array($currentSubdomain, $allowedSubdomains)) {
            abort(404, 'Subdominio no encontrado.');
        }

        return $next($request);
    }
}