<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $userRole = Auth::user()->role;

        // Nếu user có quyền nằm trong danh sách cho phép thì đi tiếp
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Nếu là user thường hoặc đang chờ duyệt, đẩy về trang chủ
        return redirect('/')->with('error', 'Bạn không có quyền truy cập trang quản trị.');
    }
}