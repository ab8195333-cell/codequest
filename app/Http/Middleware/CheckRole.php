<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * التعامل مع الطلبات الواردة والتحقق من الأدوار والصلاحيات.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. إذا لم يكن المستخدم مسجلاً دخوله إطلاقاً
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً للوصول إلى هذه الصفحة.');
        }

        $user = Auth::user();

        // 2. معالجة مصفوفة الأدوار الممررة (تفكيك القيم المدمجة إن وجدت)
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
        }

        // تنظيف المسافات الزائدة حول أسماء الأدوار
        $allowedRoles = array_map('trim', $allowedRoles);

        // 3. التحقق الصريح: هل دور المستخدم الحالي موجود ضمن الأدوار المسموح بها؟
        if (in_array($user->role, $allowedRoles, true)) {
            return $next($request); // السماح بالمرور إذا كان الأدوار متطابقة (مثل Admin)
        }

        // 4. إذا كان مستخدماً عادياً (User) وحاول دخول صفحة الأدمن أو المشرف، يُمنع فوراً
        abort(403, 'عذراً، هذه الصفحة مخصصة فقط للمصرح لهم (' . implode(' أو ', $allowedRoles) . ').');
    }
}