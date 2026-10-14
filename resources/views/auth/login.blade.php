@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center vh-100 m-auto">
        <div class="card border-0 ">
            <h3 class="text-center mb-3 fw-bold text-primary">CodeQuest</h3>

            <!-- أزرار تسجيل الدخول عبر Google و GitHub باستخدام كلاسات Bootstrap فقط -->
            <div class="d-grid gap-2 mb-3">
                <!-- Google Login Button -->
                <a href="{{ route('social.redirect', 'google') }}"
                    class="btn btn-outline-secondary py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/3840px-Google_%22G%22_logo.svg.png"
                        alt="Google Logo" style="width: 20px; height: 20px;">تسجيل الدخول بواسطة Google
                </a>

                <!-- GitHub Login Button -->
                <a href="{{ route('social.redirect', 'github') }}"
                    class="btn btn-dark py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg"
                        alt="GitHub Logo" style="width: 20px; height: 20px; filter: invert(1);"> تسجيل الدخول بواسطة GitHub
                </a>
            </div>


            <div class="card p-4 shadow" style="width: 22rem;">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>

                        <input type="email" class="form-control @error('email') is-invalid @enderror text-end" id="email"
                            placeholder="أدخل بريدك الإلكتروني" name="email" value="{{ old('email') }}" required
                            autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            placeholder="أدخل كلمة المرور" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">تذكرني</label>
                    </div>



                    <button type="submit" class="btn btn-primary w-100">
                        {{ __('تسجيل الدخول') }}
                    </button>

                    @if (Route::has('password.request'))
                        <p class="text-center mt-3">
                            نسيت كلمة المرور؟ <a href="{{ route('password.request') }}">انقر هنا</a><br>
                    @endif
                    ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
                    </p>

                </form>
            </div>
        </div>
    </div>
@endsection