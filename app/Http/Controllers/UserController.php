<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Helpers\Sanitizer;

class UserController extends Controller
{
    public function update_profile(Request $request)
    {
        $user = Auth::user();

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'title' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'about_me' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $aboutMe = Sanitizer::clean($request->about_me);
        // تحديث الحقول النصية
        $user->name = $request->name;
        $user->email = $request->email;
        $user->title = $request->title;
        $user->location = $request->location;
        $user->about_me = $aboutMe;

        // معالجة رفع الصورة القديمة والجديدة
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا كانت موجودة ليست مجرد افتراضية
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // حفظ الصورة الجديدة
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        // إعادة التوجيه لنفس صفحة التعديل مع رسالة نجاح
        return redirect()->route('profile.edit')->with('success', 'تم تحديث الملف الشخصي بنجاح!');
    }

    public function profile(?User $user = null)
    {
        // إذا لم يُحدد مستخدم في الرابط، استعرض بروفايل المستخدم المسجل حالياً
        $targetUser = $user ?? Auth::user();

        if (!$targetUser) {
            abort(404, 'المستخدم غير موجود');
        }

        // 1. تحميل الأعداد الإجمالية الأساسية
        $targetUser->loadCount([
            'questions',
            'answers',
        ]);

        // 2. تحميل العلاقات مع التصويتات
        $targetUser->load([
            'questions' => function ($q) {
                $q->latest()
                    ->withCount('answers')
                    ->withCount([
                        'votes as up_votes_count' => fn($query) => $query->where('vote_type', 'up'),
                        'votes as down_votes_count' => fn($query) => $query->where('vote_type', 'down'),
                    ]);
            },
            'answers' => function ($a) {
                $a->latest()
                    ->withCount([
                        'votes as up_votes_count' => fn($query) => $query->where('vote_type', 'up'),
                        'votes as down_votes_count' => fn($query) => $query->where('vote_type', 'down'),
                    ]);
            },
            'answers.question',
        ]);

        // 3. حساب إجمالي الوصول والانتشار (Reach)
        $totalQuestionViews = $targetUser->questions->sum('views_count');

        $totalAnswerViews = $targetUser->answers
            ->pluck('question')
            ->filter()
            ->unique('id') // التأكد من الفلترة باستخدام معرف السؤال فريداً
            ->sum('views_count');

        $totalUserReach = $totalQuestionViews + $totalAnswerViews;

        // معرفة ما إذا كان الزائر هو صاحب الملف الشخصي نفسه
        $isOwner = Auth::check() && Auth::id() === $targetUser->id;

        return view('profile.index', [
            'user' => $targetUser,
            'totalUserReach' => $totalUserReach,
            'isOwner' => $isOwner,
        ]);
    }

    public function edit_profile()
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }
}
