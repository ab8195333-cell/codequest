<?php

namespace App\Providers;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Question;
use App\Models\Setting;
use App\Policies\ContentPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            // 1. تحديث اسم المنصة
            $siteName = Setting::where('key', 'site_name')->value('value');
            if ($siteName) {
                Config::set('app.name', $siteName);
            }

            // 2. تحديث إيميل الدعم في الإعدادات ومشاركته مع كل ملفات ال-Blade
            $supportEmail = Setting::where('key', 'support_email')->value('value');
            if ($supportEmail) {
                Config::set('mail.from.address', $supportEmail);
                View::share('supportEmail', $supportEmail); // يتيح استخدام $supportEmail في أي قالب
            }
        }
        Paginator::useBootstrapFive();
        Gate::policy(Question::class, ContentPolicy::class);
        Gate::policy(Answer::class, ContentPolicy::class);
        Gate::policy(Comment::class, ContentPolicy::class);

        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
