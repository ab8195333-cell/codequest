<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class UserModerationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select('id', 'name', 'email', 'role', 'is_active', 'reputation_points', 'created_at')
                ->withCount([
                    'reportsOnQuestions as questions_reports_count',
                    'reportsOnAnswers as answers_reports_count',
                    'reportsOnComments as comments_reports_count',
                ])
                ->latest('created_at')
                ->get()
                ->map(function ($user) {
                    // تجميع إجمالي البلاغات في متغير واحد
                    $user->reports_count = $user->questions_reports_count
                        + $user->answers_reports_count
                        + $user->comments_reports_count;

                    // إخفاء الحقول الفرعية لتنظيف الـ JSON
                    $user->makeHidden([
                        'questions_reports_count',
                        'answers_reports_count',
                        'comments_reports_count',
                    ]);

                    return $user;
                });

            // تغليف النتيجة داخل مفتاح data لتتوافق مع DataTables
            return response()->json([
                'data' => $users,
            ], 200);
        }

        return view('dashboard.Moderator.users.index');
    }

    // إرسال تحذير للمستخدم عبر جدول NOTIFICATIONS
    public function sendWarning(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($id);

        // استخدام دالة Notification::send() المعرفة لديك
        // يرسل الإشعار لقاعدة البيانات + إيميل للمستخدم تلقائياً (إن كان مفصلاً لديه)
        Notification::send(
            $user->id,                             // معرف المستخدم
            $request->message,                     // نص التحذير
            'warning'                              // نوع الإشعار (warning/info)
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال التحذير للمستخدم بنجاح وإشعاره بريدياً.',
        ]);
    }

    // تغيير حالة الحظر (تفعيل / تعطيل الحساب via is_active)
    public function toggleBan(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // حماية عدم حظر المشرفين أو الآدمن
        if ($user->role === 'admin' || $user->role === 'moderator') {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكنك حظر حسابات الإدارة أو الإشراف.',
            ], 403);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $actionText = $user->is_active ? 'تفعيل حساب' : 'حظر';

        return response()->json([
            'status' => 'success',
            'message' => "تم {$actionText} المستخدم بنجاح.",
        ]);
    }
}
