<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Helpers\Sanitizer;
use Illuminate\Support\Facades\DB;
use App\Models\Question;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AnswerController extends Controller
{
    /**
     * حفظ إجابة جديدة
     */
    public function storeAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer'      => 'nullable|string',
            'answer_text' => 'nullable|string',
        ]);

        $answerContent = $request->input('answer') ?? $request->input('answer_text');

        if (empty(trim($answerContent))) {
            return redirect()->back()->with(['error' => 'محتوى الإجابة مطلوب.']);
        }

        if (auth()->check()) {
            $cleanDescription = Sanitizer::clean($answerContent);
            
            $answer = new Answer();
            $answer->question_id = $request->question_id;
            $answer->user_id     = auth()->id();
            $answer->answer_text = $cleanDescription;
            $answer->save();

            // إرسال إشعار لصاحب السؤال (إذا لم يكن هو نفسه صاحب الإجابة)
            $question = Question::find($request->question_id);
            if ($question && $question->user_id !== auth()->id()) {
                Notification::send(
                    $question->user_id,
                    'قام ' . auth()->user()->name . ' بالإجابة على سؤالك.',
                    'answer',
                    $question->id,
                    Question::class
                );
            }

            return redirect()->back()->with(['success' => 'تم إضافة الإجابة بنجاح.']);
        } else {
            return redirect()->back()->with(['error' => 'يجب تسجيل الدخول لإضافة إجابة.']);
        }
    }

    /**
     * تحديد / إلغاء تحديد الإجابة المقبولة (الحل المعتمد)
     */
    public function acceptAnswer(Request $request, $id)
    {
        // استخدام البحث المباشر بعمود id لمنع تبديل لاراول للعمود افتراضياً
        $answer = Answer::where('id', $id)->firstOrFail();
        $question = $answer->question;

        if (auth()->id() !== $question->user_id) {
            abort(403);
        }

        $isAccepted = $request->input('is_accepted') == '1';

        DB::transaction(function () use ($answer, $isAccepted) {
            $answer->update(['is_accepted' => $isAccepted]);

            // محرك السمعة: إضافة / إنقاص 15 نقطة لصاحب الإجابة
            if ($isAccepted) {
                $answer->user->increment('reputation_points', 15);
            } else {
                $answer->user->decrement('reputation_points', 15);
            }
        });

        if ($answer->user_id !== auth()->id()) {
            Notification::send(
                $answer->user_id,
                'تم ' . ($isAccepted ? 'قبول' : 'رفض') . ' إجابتك على السؤال: "' . $question->title . '".',
                'answer_status',
                $answer->id,
                Answer::class
            );
        }

        return back()->with('success', 'تم تحديث حالة الإجابة.');
    }

    /**
     * تحديث الإجابة
     */
    public function update(Request $request, Answer $answer)
    {
        // التحقق من الملكية أو الصلاحيات الإدارية
        if ($answer->user_id !== auth()->id() && !auth()->user()->isStaff()) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذه الإجابة.'], 403);
        }

        $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $cleanDescription = Sanitizer::clean($request->content);

        $answer->update([
            'answer_text' => $cleanDescription,
        ]);

        if (auth()->user()->isStaff() && $answer->user_id !== auth()->id()) {
            $question = $answer->question;
            if ($question) {
                Notification::send(
                    $answer->user_id,
                    'قام المشرف ' . auth()->user()->name . ' بتعديل إجابتك.',
                    'answer',
                    $question->id,
                    Question::class
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإجابة بنجاح.'
        ]);
    }

    /**
     * حذف الإجابة
     */
    public function destroy(Answer $answer)
    {
        // التحقق من الملكية أو الصلاحيات الإدارية
        if ($answer->user_id !== auth()->id() && !auth()->user()->isStaff()) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذه الإجابة.'], 403);
        }

        $question = $answer->question;

        if (auth()->user()->isStaff() && $question && $answer->user_id !== auth()->id()) {
            Notification::send(
                $answer->user_id,
                'قام المشرف ' . auth()->user()->name . ' بحذف إجابتك على سؤال: "' . $question->title . '".',
                'answer',
                $question->id,
                Question::class
            );
        }

        $answer->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإجابة بنجاح.'
        ]);
    }
}