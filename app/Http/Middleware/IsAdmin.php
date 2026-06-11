<?php
// app/Http/Middleware/IsAdmin.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Admin only.'], 403);
            }
            return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki akses admin.');
        }

        return $next($request);
    }
}

// ============================================================
// app/Http/Middleware/CheckBlacklist.php
// ============================================================
// namespace App\Http\Middleware;
//
// use Closure;
// use Illuminate\Http\Request;
//
// class CheckBlacklist
// {
//     public function handle(Request $request, Closure $next)
//     {
//         if (auth()->check() && auth()->user()->is_blacklisted) {
//             auth()->logout();
//             return redirect()->route('login')
//                 ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
//         }
//         return $next($request);
//     }
// }
