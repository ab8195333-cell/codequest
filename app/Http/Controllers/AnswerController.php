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
   public function storeAnswer(Request $request){

    $request->validate([
        'question_id' => 'required|exists:questions,question_id',
        'answer' => 'required|string',
    ]);
    if (auth()->check()) {
        // في دالة store أو update:
        $cleanDescription = Sanitizer::clean($request->answer);
        $answer = new Answer();
        $answer->question_id = $request->question_id;
        $answer->user_id = auth()->id();
        $answer->answer_text = $cleanDescription;
        $answer->save();
// بعد حفظ الإجابة
        $question = Question::find($request->question_id);
        Notification::send(
            $question->user_id,
            'قام ' . auth()->user()->name . ' بالإجابة على سؤالك.',
            'answer',
            $question->question_id,
            Question::class
        );
        return redirect()->back()->with([ 'success' => 'تم إضافة الإجابة بنجاح.']);
    } else {
        return redirect()->back()->with(['error' => 'يجب تسجيل الدخول لإضافة إجابة.']);


    }
}
public function acceptAnswer(Request $request, $id)
{
    $answer = Answer::where('answer_id', $id)->firstOrFail();
    $question = $answer->question;

    if (auth()->id() !== $question->user_id) {
        abort(403);
    }

    $isAccepted = $request->input('is_accepted') == '1';

    DB::transaction(function () use ($answer, $isAccepted) {
        $answer->update(['is_accepted' => $isAccepted]);

        // محرك السمعة: إضافة/إنقاص 15 نقطة لصاحب الإجابة
        if ($isAccepted) {
            $answer->user->increment('reputation_points', 15);
        } else {
            $answer->user->decrement('reputation_points', 15);
        }
    });
    Notification::send(
        $answer->user_id,
        'تم ' . ($isAccepted ? 'قبول' : 'رفض') . ' إجابتك على السؤال: "' . $question->title . '".',
        'answer_status',
        $answer->answer_id,
        Answer::class
    );

    return back()->with('success', 'تم تحديث حالة الإجابة.');
}
public function update(Request $request, Answer $answer)
    {
        // التحقق من الملكية
        if ($answer->user_id !== auth()->id()&& !auth()->user()->isStaff()) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذه الإجابة.'], 403);
        }
        // التحقق من البيانات المدخلة
        $request->validate([
            'content' => 'required|string|min:5',
        ]);
        $cleanDescription = Sanitizer::clean($request->content);

        // تحديث المحتوى
        $answer->update([
            'answer_text' => $request->content,
        ]);
        if (auth()->user()->isStaff()) {

        $question = Question::find($request->question_id);
        Notification::send(
            $question->user_id,
            'قام المشرف' . auth()->user()->name . ' بتعديل إجابتك على سؤالك.',
            'answer',
            $question->question_id,
            Question::class
        );
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
        // التحقق من الملكية
        if ($answer->user_id !== auth()->id()&& !auth()->user()->isStaff()) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذه الإجابة.'], 403);
        }

        $answer->delete();
        if (auth()->user()->isStaff()) {

            $question = Question::find($request->question_id);
            Notification::send(
                $question->user_id,
                'قام المشرف' . auth()->user()->name . ' بتعديل إجابتك على سؤالك.',
                'answer',
                $question->question_id,
                Question::class
            );
            }
        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإجابة بنجاح.'
        ]);
    }
}