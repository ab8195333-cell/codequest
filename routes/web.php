<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\Moderator\CategoryController;
use App\Http\Controllers\Moderator\ModeratorDashboardController;
use App\Http\Controllers\Moderator\TagController;
use App\Http\Controllers\Moderator\UserModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuestionController;
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Moderator\ReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| المسارات العامة (Public Routes)
|--------------------------------------------------------------------------
*/
Route::get('/tags', [HomeController::class, 'indextag'])->name('tags&categories');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/questions', [HomeController::class, 'index']);
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/questions/{id}', [QuestionController::class, 'show'])->name('questions.show');

Route::get('/ai-assistant', [AiAssistantController::class, 'index'])->name('ai.assistant.index');
Route::post('/ai-assistant/analyze', [AiAssistantController::class, 'analyze'])->name('ai.assistant.analyze')->middleware('throttle:10,1');
// المصادقة الخارجية
Route::prefix('auth/{provider}')->where(['provider' => 'google|github'])->group(function () {
    Route::get('/redirect', [SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');
});

Auth::routes();
// Auth::routes(['verify' => true]);
/*
|--------------------------------------------------------------------------
| مسارات المستخدمين المسجلين (Authenticated Routes)
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'active')->group(function () {

    // 🟢 1. مسارات القراءة والتصفح (بدون تقييد شديد)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/ask', [QuestionController::class, 'indexAsk'])->name('ask');
    Route::get('/question/edit/{question}', [QuestionController::class, 'updateindex'])->name('question.edit')->middleware('can:update,question');
    Route::get('/profile/{user?}', [UserController::class, 'profile'])->where('user', '[0-9]+')->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit_profile'])->name('profile.edit');
    Route::post('/toggleEmailNotifications', [NotificationController::class, 'toggleEmailNotifications'])->name('profile.notifications.toggle');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notification/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    /*
    |--------------------------------------------------------------------------
    | 🔴 العمليات التي تتطلب حماية وتحديد سرعة (Throttled Operations)
    | حد موحد: 10 عمليات في الدقيقة لكل مستخدم على هذه مجموعة المسارات
    |--------------------------------------------------------------------------
    */
    Route::middleware('throttle:10,1')->group(function () {
        // إنشاء وتحديث البيانات
        Route::post('/ask', [QuestionController::class, 'store'])->name('ask.store');
        Route::put('/question/update/{question}', [QuestionController::class, 'updateQ'])->name('question.update')->middleware('can:update,question');
        Route::delete('/question/delete/{id}', [QuestionController::class, 'deleteQ'])->name('question.destroy');

        // الإجابات والتعليقات
        // مسارات الإجابات (Answers)
        Route::post('/question/answer', [AnswerController::class, 'storeAnswer'])->name('questions.answer');
        Route::put('/question/answer/{id}', [AnswerController::class, 'acceptAnswer'])->name('answer.accept');
        Route::put('/answers/{answer}', [AnswerController::class, 'update'])->name('answers.update');
        Route::delete('/answers/{answer}', [AnswerController::class, 'destroy'])->name('answers.destroy');

        // مسارات التعليقات (Comments)
        Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/comments/{type}/{id}', [CommentController::class, 'store'])->name('comments.store');

        // التفاعلات والتعديلات
        Route::post('report', [ReportController::class, 'storeReport'])->name('report.store');
        Route::post('/vote', [VoteController::class, 'vote'])->name('vote');
        Route::put('/profile/update', [UserController::class, 'update_profile'])->name('profile.update');
        Route::post('/upload-editor-image', [ImageUploadController::class, 'upload'])->name('upload.editor.image');
    });
});

/*
|--------------------------------------------------------------------------
| مسارات لوحة التحكم الإدارية
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        // إعدادات النظام والـ AI
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('settings.index');
            Route::post('/general', [SettingController::class, 'updateGeneral'])->name('settings.updateGeneral');
            Route::post('/ai', [SettingController::class, 'updateAi'])->name('settings.updateAi');
            Route::post('/settings/backup', [SettingController::class, 'backup'])->name('settings.backup');
            Route::get('backup/download/{fileName}', [SettingController::class, 'downloadBackup'])->name('backup.download');
            Route::delete('backup/delete/{fileName}', [SettingController::class, 'deleteBackup'])->name('backup.delete');
            Route::post('/backup', [SettingController::class, 'runBackup'])->name('settings.backup');
        });
        // مسار صفحة الإحصائيات التحليلية
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        // إدارة المستخدمين والصلاحيات
        Route::prefix('users')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('users.index');
            Route::patch('/{id}/role', [RoleController::class, 'updateRole'])->name('users.update-role');
            Route::patch('/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('users.toggle-status');
        });
    });
    Route::prefix('moderator')->middleware(['auth', 'role:admin,moderator'])->group(function () {
        // المسار الرئيسي لصفحة المشرفين (Index)
        Route::get('/', [ModeratorDashboardController::class, 'index'])->name('index');
        // ==========================================
        // مسارات إدارة البلاغات (Reports Management)
        // ==========================================
        Route::prefix('reports')->name('reports.')->group(function () {
            // مسار موحد لصفحة البلاغات وجلب بيانات DataTables عبر AJAX
            Route::get('/', [ReportController::class, 'index'])->name('index');
            // 3. تحديث حالة البلاغ (قبول/تجاهل أو حذف المحتوى)
            Route::put('/{id}/status', [ReportController::class, 'updateStatus'])->name('updateStatus');

            // 4. حذف بلاغ مفرد أو مجمع
            Route::delete('/{id}', [ReportController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('users')->name('users.')->group(function () {

            Route::get('/', [UserModerationController::class, 'index'])->name('index');
            Route::post('/{id}/warning', [UserModerationController::class, 'sendWarning'])->name('warning');
            Route::put('/{id}/toggle-ban', [UserModerationController::class, 'toggleBan'])->name('toggleBan');
        });
        // إدارة التصنيفات والوسوم
        Route::resource('categories', CategoryController::class);
        Route::resource('tags', TagController::class);
    });
});
