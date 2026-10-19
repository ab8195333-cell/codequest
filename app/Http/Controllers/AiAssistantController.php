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

        // جلب مفتاح Gemini API من إعدادات Render أو Supabase
        $apiKey = env('GEMINI_API_KEY') 
                  ?? Setting::where('key', 'gemini_api_key')->value('value')
                  ?? config('services.gemini.api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إعداد مفتاح Gemini API بشكل صحيح.',
            ], 400);
        }

        $adminSystemPrompt = Setting::where('key', 'system_prompt')->value('value')
                             ?? 'You are a helpful AI coding assistant for CodeQuest.';

        try {
            // التحديث هنا: استخدام نموذج gemini-3.8-flash المعتمد حالياً
            $response = Http::timeout(60)
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $adminSystemPrompt . "\n\nCode/Question:\n" . $request->code]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text') ?? 'تمت المعالجة بنجاح.';

                return response()->json([
                    'success' => true,
                    'data' => $reply,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ من خادم Gemini: ' . $response->json('error.message', 'خطأ غير معروف'),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بخدمة Gemini.',
            ], 500);
        }
    }
}