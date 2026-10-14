<?php

use App\Http\Middleware\CheckActiveUser;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: function (Request $request) {
                session()->flash('error', 'يجب عليك تسجيل الدخول أولاً للوصول إلى هذه الصفحة.');

                return route('login');
            }
        );
        // 1. تسجيل جميع الـ Aliases في مصفوفة واحدة
        $middleware->alias([
            'role' => CheckRole::class,
            'active' => CheckActiveUser::class,
        ]);

        // 2. تسجيل الـ Global Middleware على شبكة الـ Web
        $middleware->web(append: [
            CheckMaintenanceMode::class,
        ]);

        // // 3. إعداد الـ Proxies
        // $middleware->trustProxies(at: '*');
//         'providers' => [
//     // ...مُقدمين آخرين
//     Illuminate\View\ViewServiceProvider::class,
// ],
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
