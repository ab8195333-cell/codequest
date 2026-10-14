<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    public function index()
    {
        return view('ai-assistant.index');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'history' => 'nullable|array',
        ]);

        // 1. جلب رابط الـ Webhook (من قاعدة البيانات أو القيمة الافتراضية من config)
        $webhookUrl = Setting::where('key', 'n8n_webhook_url')->value('value')
                      ?? config('services.n8n.webhook_url');

        // 2. جلب أمر الـ System Prompt (من قاعدة البيانات أو القيمة الافتراضية من config)
        $adminSystemPrompt = Setting::where('key', 'system_prompt')->value('value')
                             ?? config('services.ai.system_prompt');

        if (! $webhookUrl) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إعداد رابط الـ Webhook الخاص بـ n8n.',
            ], 400);
        }

        try {
            $response = Http::timeout(60)->post($webhookUrl, [
                'code' => $request->code,
                'system_prompt' => $adminSystemPrompt,
                'history' => $request->history ?? [],
                'username' => auth()->user()->name ?? 'guest',
            ]);

            if ($response->successful()) {
                $rawOutput = $response->json('output') ?? $response->body();

                return response()->json([
                    'success' => true,
                    'data' => $rawOutput,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الطلب من خادم الذكاء الاصطناعي.',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بالخادم، يرجى التأكد من تشغيل n8n.',
            ], 500);
        }
    }
}
