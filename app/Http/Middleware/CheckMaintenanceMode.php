<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // جلب حالة الصيانة من قاعدة البيانات
        $isMaintenance = Setting::where('key', 'maintenance_mode')->value('value');

        if ($isMaintenance == '1') {
            // استثناء مسارات لوحة التحكم وتسجيل الدخول
            if ($request->is('admin*') || $request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // إذا كان المستخدم ليس أدمين، يتم إظهار صفحة الصيانة 503
            if (! auth()->check() || auth()->user()->role !== 'admin') {
                abort(503, 'المنصة حالياً في وضع الصيانة لتحديث بعض الميزات. سنعود قريباً!');
            }
        }

        return $next($request);
    }
}
