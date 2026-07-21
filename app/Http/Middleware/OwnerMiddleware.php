<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isOwner()) {
            return redirect('/')->with('error', 'Hanya Owner yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
