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

        // جلب مفتاح OpenAI API من قاعدة البيانات أو البيئة .env
        $apiKey = Setting::where('key', 'openai_api_key')->value('value')
                  ?? config('services.openai.api_key') 
                  ?? env('OPENAI_API_KEY');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إعداد مفتاح OpenAI API بشكل صحيح.',
            ], 400);
        }

        // جلب تعليمات النظام System Prompt
        $adminSystemPrompt = Setting::where('key', 'system_prompt')->value('value')
                             ?? config('services.ai.system_prompt')
                             ?? 'You are a helpful AI coding assistant for CodeQuest.';

        try {
            // إرسال الطلب مباشرة إلى OpenAI API
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $adminSystemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => "Code/Question:\n" . $request->code
                        ]
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content') ?? 'تمت المعالجة بنجاح.';

                return response()->json([
                    'success' => true,
                    'data' => $reply,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ من خادم OpenAI: ' . $response->json('error.message', 'خطأ غير معروف'),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بخدمة OpenAI.',
            ], 500);
        }
    }
}