<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // التوجيه إلى مزود الخدمة (Google أو GitHub)
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // استقبال الـ Callback بعد موافقة المستخدم
    public function handleProviderCallback($provider)
{
    try {
        $socialUser = Socialite::driver($provider)->user();

        $user = User::where('provider_id', $socialUser->getId())
                    ->where('provider_name', $provider)
                    ->first();

        if (!$user) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'provider_name' => $provider,
                    'provider_id'   => $socialUser->getId(),
                ]);
            } else {
                $user = User::create([
                    'name'          => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email'         => $socialUser->getEmail(),
                    'provider_name' => $provider,
                    'provider_id'   => $socialUser->getId(),
                    'password'      => null,
                ]);
            }
        }

        Auth::login($user, true);

        return redirect()->intended('/');

    }
    // catch (\Exception $e) {
    //     // اطبع الخطأ مباشرة لمعرفة السبب الدقيق
    //     dd($e->getMessage());
    // }
catch (\Exception $e) {
    // dd('حدث خطأ أثناء تسجيل الدخول عبر ' . $provider . ': ' . $e->getMessage());
    return redirect()->route('login')->with('error', 'حدث خطأ أثناء تسجيل الدخول، يرجى المحاولة لاحقاً.');
}
}
}
