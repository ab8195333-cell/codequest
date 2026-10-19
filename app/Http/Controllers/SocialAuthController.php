<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // التوجيه إلى مزود الخدمة (Google)
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // استقبال الـ Callback بعد موافقة المستخدم
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // 1. البحث عن المستخدم بواسطة google_id أولاً
            $user = User::where('google_id', $socialUser->getId())->first();

            if (!$user) {
                // 2. إذا لمط وجدنا بـ google_id، نبحث بالبريد الإلكتروني
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    // ربط الحساب القديم بمعرف جوجل وصورته
                    $user->update([
                        'google_id' => $socialUser->getId(),
                        'avatar'    => $socialUser->getAvatar(),
                    ]);
                } else {
                    // 3. إنشاء مستخدم جديد تماماً
                    $user = User::create([
                        'name'     => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                        'email'    => $socialUser->getEmail(),
                        'google_id'=> $socialUser->getId(),
                        'avatar'   => $socialUser->getAvatar(),
                        'password' => Hash::make(Str::random(24)), // كلمة مرور عشوائية ومشفرة لأن الحقل nullable
                    ]);
                }
            }

            // تسجيل الدخول
            Auth::login($user, true);

            return redirect()->intended('/');

        } catch (\Exception $e) {
            // في حال حدوث أي خطأ غير متوقع، يعيد التوجيه لصفحة الدخول مع رسالة
            return redirect()->route('login')->with('error', 'حدث خطأ أثناء تسجيل الدخول، يرجى المحاولة لاحقاً.');
        }
    }
}