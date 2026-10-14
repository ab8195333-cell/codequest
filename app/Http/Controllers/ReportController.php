<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Question;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // عند طلب AJAX من DataTables
        if ($request->ajax()) {
            $reports = Report::with(['user', 'reportable'])->latest()->get();

            // DataTables تتطلب تغليف المصفوفة داخل المفتاح 'data'
            return response()->json([
                'data' => $reports,
            ], 200);
        }

        // إذا كان طلب متصفح عادي، ارجع الصفحة فقط
        return view('dashboard.Moderator.reports.index');
    }

    // 3. اتخاذ إجراء على البلاغ (إغلاق البلاغ أو قبول/حذف المحتوى)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:resolved,dismissed', // resolved: تم المعالجة (حذف)، dismissed: تجاهل البلاغ
            'delete_content' => 'nullable|boolean',
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        // إذا أقر المشرف حذف المحتوى الأصلي أيضاً
        if ($request->delete_content && $report->reportable) {
            $report->reportable->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث حالة البلاغ بنجاح.',
        ]);
    }

    // 4. حذف البلاغ مفرد أو مجمع
    public function destroy($id)
    {
        $ids = explode(',', $id);
        Report::whereIn('report_id', $ids)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف البلاغ/البلاغات بنجاح.',
        ]);
    }

    public function storeReport(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة ومطابقتها مع نماذج (Models) النظام
        $validated = $request->validate([
            'id' => 'required|integer',
            'type' => ['required', 'string', Rule::in(['question', 'answer', 'comment'])],
            'reason' => 'required|string|max:100',
            'details' => 'nullable|string|max:1000',
        ]);

        // 2. خريطة لربط النوع المستلم باسم الـ Model الخاص به (Polymorphic Mapping)
        $typeMap = [
            'question' => Question::class,
            'answer' => Answer::class,
            'comment' => Comment::class,
        ];

        $modelClass = $typeMap[$validated['type']];

        // 3. التأكد من وجود العنصر المُبلاغ عنه فعلياً في قاعدة البيانات
        $modelKeyName = (new $modelClass)->getKeyName(); // يجلب اسم الـ Primary Key الخاص بكل جدول (question_id, answer_id, comment_id)
        $itemExists = $modelClass::where($modelKeyName, $validated['id'])->exists();

        if (! $itemExists) {
            return response()->json([
                'success' => false,
                'message' => 'العنصر المراد الإبلاغ عنه غير موجود.',
            ], 404);
        }

        // 4. دمج السبب الرئيسي مع التفاصيل الإضافية للتخزين بداخل حقل reason
        $fullReason = $validated['reason'];
        if (! empty($validated['details'])) {
            $fullReason .= ' - التفاصيل: '.$validated['details'];
        }

        // 5. حفظ التقرير بداخل قاعدة البيانات
        Report::create([
            'user_id' => auth()->id(),
            'reportable_id' => $validated['id'],
            'reportable_type' => $modelClass,
            'reason' => $fullReason,
            'status' => 'pending', // الحالة الافتراضية للبلاغ
        ]);

        // 6. إرجاع استجابة JSON تتوافق مع Axios والمودال
        return response()->json([
            'success' => true,
            'message' => 'تم إرسال البلاغ بنجاح وسيقوم المشرفون بمراجعته.',
        ], 201);
    }
}
