<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBlacklist
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_blacklisted) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }
        return $next($request);
    }
}
