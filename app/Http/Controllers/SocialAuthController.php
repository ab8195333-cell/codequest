<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // 1. البحث عن المستخدم عبر google_id أو البريد الإلكتروني
            $user = User::where('google_id', $socialUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    // تحديث بيانات المستخدم الموجود مسبقاً
                    $user->update([
                        'google_id' => $socialUser->getId(),
                        'avatar'    => $socialUser->getAvatar(),
                    ]);
                } else {
                    // إنشاء مستخدم جديد تماماً في قاعدة بيانات Supabase
                    $user = User::create([
                        'name'      => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                        'email'     => $socialUser->getEmail(),
                        'google_id' => $socialUser->getId(),
                        'avatar'    => $socialUser->getAvatar(),
                        'password'  => Hash::make(Str::random(24)),
                    ]);
                }
            }

            // 2. تسجيل دخول المستخدم في الجلسة بقوة
            Auth::login($user, true);

            // 3. التأكد من حفظ الجلسة والتوجيه المباشر للصفحة الرئيسية
            session()->regenerate();

            return redirect()->to('/');

        } catch (\Exception $e) {
            // في حال حدوث أي استثناء، سنقوم بطباعته لنراه بوضوح بدلاً من تحويله الصامت
            dd('Error in Social Login: ' . $e->getMessage());
        }
    }
}