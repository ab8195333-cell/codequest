<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
// تشغيل النسخ الاحتياطي
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $generalSettings = [
            'site_name' => Setting::where('key', 'site_name')->value('value') ?? config('app.name'),
            'support_email' => Setting::where('key', 'support_email')->value('value') ?? config('mail.from.address'),
            'maintenance_mode' => Setting::where('key', 'maintenance_mode')->value('value') ?? '0',
        ];
        // جلب اسم التطبيق كما هو معرف في إعدادات الحزمة
        $appName = config('backup.backup.name', config('app.name'));
        $aiSettings = [
            'n8n_webhook_url' => Setting::where('key', 'n8n_webhook_url')->value('value') ?? env('N8N_WEBHOOK_URL'),
            'system_prompt' => Setting::where('key', 'system_prompt')->value('value') ?? config('services.ai.system_prompt'),
        ];
        // البحث في المجلد الخاص بالتطبيق، وإن لم يوجد يجلب من الجذر
        $files = Storage::disk('local')->allFiles($appName);

        // إذا لم يجد ملفات بالمسار المباشر، ابحث في كل المجلدات داخل storage/app
        if (empty($files)) {
            $files = Storage::disk('local')->allFiles();
        }

        $backups = [];
        foreach ($files as $file) {
            // التاكد من أن الملف بصيغة zip
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'file_name' => basename($file),
                    'file_path' => $file, // المسار الكامل الداخلي
                    'file_size' => round(Storage::disk('local')->size($file) / 1024 / 1024, 2).' MB',
                    'last_modified' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                ];
            }
        }

        // ترتيب الملفات من الأحدث للأقدم
        usort($backups, function ($a, $b) {
            return strtotime($b['last_modified']) <=> strtotime($a['last_modified']);
        });

        return view('dashboard.admin.settings.index', compact('backups', 'aiSettings', 'generalSettings'));
    }

    // 1. تحديث إعدادات المنصة العامة
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'support_email' => 'required|email',
        ]);

        Setting::updateOrCreate(['key' => 'site_name'], ['value' => $request->site_name]);
        Setting::updateOrCreate(['key' => 'support_email'], ['value' => $request->support_email]);
        Setting::updateOrCreate(['key' => 'maintenance_mode'], ['value' => $request->has('maintenance_mode') ? '1' : '0']);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث إعدادات المنصة العامة بنجاح.',
        ]);
    }

    // 2. تحديث إعدادات الذكاء الاصطناعي و n8n
    // تحديث إعدادات الذكاء الاصطناعي
    public function updateAi(Request $request)
    {
        $request->validate([
            'n8n_webhook_url' => 'nullable|url',
            'system_prompt' => 'nullable|string',
        ]);

        Setting::updateOrCreate(['key' => 'n8n_webhook_url'], ['value' => $request->n8n_webhook_url]);
        Setting::updateOrCreate(['key' => 'system_prompt'], ['value' => $request->system_prompt]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث إعدادات الذكاء الاصطناعي بنجاح.',
        ]);
    }

    // 1. تشغيل النسخ الاحتياطي
    public function backup()
    {
        try {
            Artisan::call('backup:run', [
                '--only-db' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء النسخة الاحتياطية بنجاح.',
            ]);
        } catch (\Exception $e) {
            Log::error('Backup Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إجراء النسخ الاحتياطي.',
            ], 500);
        }
    }

    // 2. تنزيل نسخة احتياطية محددة
    public function downloadBackup($fileName)
    {
        // الاسم المعتاد للمجلد في Spatie هو اسم التطبيق config('app.name')
        $appName = config('backup.backup.name', config('app.name'));
        $filePath = $appName.'/'.$fileName;

        if (Storage::disk('local')->exists($filePath)) {
            return Storage::disk('local')->download($filePath);
        }

        return redirect()->back()->with('error', 'الملف غير موجود.');
    }

    // 3. حذف نسخة احتياطية
    public function deleteBackup($fileName)
    {
        $appName = config('backup.backup.name', config('app.name'));
        $filePath = $appName.'/'.$fileName;

        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);

            return response()->json(['success' => true, 'message' => 'تم حذف النسخة الاحتياطية بنجاح.']);
        }

        return response()->json(['success' => false, 'message' => 'الملف غير موجود.'], 404);
    }
}
