<?php
namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;

class CommentController extends Controller
{
    public function store(Request $request, string $type, int $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $modelMap = [
            'question' => Question::class,
            'answer'   => Answer::class,
        ];

        if (!array_key_exists($type, $modelMap)) {
            abort(404);
        }

        $model = $modelMap[$type]::findOrFail($id);

        // التنفيذ من خلال الموديل
        Comment::createWithNotification($model, $request->input('content'), $type);

        return redirect()->back()->with('success', 'تم إرسال التعليق بنجاح.');
    }

    /**
     * تحديث التعليق
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::where('id', $id)->firstOrFail();

        // التحقق من الملكية
        if ($comment->user_id !== auth()->id() && !auth()->user()->isStaff()){
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا التعليق.'], 403);
        }

        // التحقق من البيانات المدخلة
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // تحديث المحتوى
        $comment->update([
            'content' => $request->content,
        ]);

        if (auth()->user()->isStaff() && $comment->user_id !== auth()->id()) {
            Notification::send(
                $comment->user_id,
                'قام المشرف ' . auth()->user()->name . ' بتعديل تعليقك.',
                'comment',
                $comment->id,
                Comment::class
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التعليق بنجاح.'
        ]);
    }

    /**
     * حذف التعليق
     */
    public function destroy($id)
    {
        $comment = Comment::where('id', $id)->firstOrFail();

        // التحقق من الملكية
        if ($comment->user_id !== auth()->id() && !auth()->user()->isStaff()) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذا التعليق.'], 403);
        }

        $commentUser = $comment->user_id;
        $comment->delete();

        if (auth()->user()->isStaff() && $commentUser !== auth()->id()) {
            Notification::send(
                $commentUser,
                'قام المشرف ' . auth()->user()->name . ' بحذف تعليقك.',
                'comment',
                $id,
                Comment::class
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التعليق بنجاح.'
        ]);
    }
}