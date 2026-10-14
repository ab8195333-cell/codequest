<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Notification;
use App\Models\Question;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function vote(Request $request)
{
    if (! auth()->check()) {
        return response()->json(['error' => 'يجب عليك تسجيل الدخول لتتمكن من التصويت.'], 401);
    }

    try {
        $type = $request->input('type');
        $id = $request->input('id');
        $voteType = $request->input('vote_type'); // 'up' أو 'down'

        // جلب السؤال أو الإجابة
        $item = ($type === 'question')
            ? Question::where('question_id', $id)->first()
            : Answer::where('answer_id', $id)->first();

        if (! $item) {
            return response()->json(['error' => 'المحتوى غير موجود!'], 404);
        }

        // منع صاحب المحتوى من التصويت لنفسه
        if ($item->user_id === auth()->id()) {
            return response()->json(['error' => 'لا يمكنك التصويت على المحتوى الخاص بك!'], 403);
        }

        // جلب صاحب المحتوى
        $author = $item->user ?? User::find($item->user_id);

        $existingVote = $item->votes()->where('user_id', auth()->id())->first();

        if ($existingVote) {
            // 🟢 الحالة 1: الضغط على نفس الزر مرتين (إلغاء التصويت)
            if ($existingVote->vote_type === $voteType) {
                if ($author) {
                    $this->updateReputation($author, $item, $existingVote->vote_type, 'remove');
                }
                $existingVote->delete();
                $userVote = null;
            }
            // 🟢 الحالة 2: التحويل المباشر (من سلبي إلى إيجابي أو العكس)
            else {
                if ($author) {
                    $this->updateReputation($author, $item, $existingVote->vote_type, 'remove');
                    $this->updateReputation($author, $item, $voteType, 'add');
                }
                $existingVote->update(['vote_type' => $voteType]);
                $userVote = $voteType;
            }
        } else {
            // 🟢 الحالة 3: تصويت جديد لأول مرة
            $item->votes()->create([
                'user_id'   => auth()->id(),
                'vote_type' => $voteType,
            ]);

            if ($author) {
                $this->updateReputation($author, $item, $voteType, 'add');
            }
            $userVote = $voteType;
        }

        // حساب صافي الأصوات للواجهة
        $upVotes = $item->votes()->where('vote_type', 'up')->count();
        $downVotes = $item->votes()->where('vote_type', 'down')->count();
        $netVotes = $upVotes - $downVotes;
  // تأخير الاستجابة لمدة 3 ثوانٍ لمعاينة زر الانتظار

        return response()->json([
            'success'   => true,
            'net_votes' => $netVotes,
            'user_vote' => $userVote,
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'خطأ بالسيرفر: '.$e->getMessage()], 500);
    }
}

/**
 * دالة محرك السمعة الدقيقة وإرسال التنبيهات
 */
private function updateReputation($user, $item, $voteType, $action = 'add')
{
    if (! $user) {
        return;
    }

    if (is_null($user->reputation_points)) {
        $user->reputation_points = 0;
        $user->save();
    }

    $points = ($voteType === 'up') ? 10 : 5;

    // 1. تحديث النقاط (زيادة أو نقصان)
    if ($action === 'add') {
        if ($voteType === 'up') {
            $user->increment('reputation_points', $points);
        } else {
            $user->decrement('reputation_points', $points);
        }

        // 2. إرسال الإشعار فقط عند "إضافة" تصويت جديد
        $voteLabel   = ($voteType === 'up') ? 'بالإعجاب بـ' : 'بعدم الإعجاب بـ';
        $targetLabel = ($item instanceof Question) ? 'سؤالك' : 'إجابتك';
        $message     = 'قام ' . auth()->user()->name . " {$voteLabel} {$targetLabel}.";

        Notification::send(
            $user->id,             // ID المستلم
            $message,                   // الرسالة
            'vote',                     // نوع الإشعار
            $item->getKey(),            // ID السؤال/الإجابة
            get_class($item)            // Model Class
        );

    } else {
        // عند إلغاء التصويت: يتم سحب النقاط فقط بدون إرسال إشعار جديد
        if ($voteType === 'up') {
            $user->decrement('reputation_points', $points);
        } else {
            $user->increment('reputation_points', $points);
        }
    }
}
}