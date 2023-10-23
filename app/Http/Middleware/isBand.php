<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isBand
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!(\auth()->user()->isAdmin())){
            if (\auth()->check() && !(\auth()->user()->isBand()) ){

                return response()->json([
                    'error' => 'Account is disable',
                ],403);
            }
        }

        return $next($request);
    }
}
