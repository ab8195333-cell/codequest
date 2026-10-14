@extends('layouts.app')

@section('content')
@if (session('error'))
    <!-- Bootstrap Modal للخطأ -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>خطأ
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{ session('error') }}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>

            </div>
        </div>
    </div>

    <!-- تشغيل الموديل تلقائياً عند تحميل الصفحة -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    </script>
@endif
    <div class="container d-flex justify-content-center align-items-center vh-100 m-auto">
        <div class="card border-0">
            <h3 class="text-center mb-3 fw-bold text-primary">CodeQuest</h3>

            <!-- أزرار التسجيل عبر Google و GitHub -->
            <div class="d-grid gap-2 mb-3">
                <!-- Google Sign up Button -->
                <a href="{{ route('social.redirect', 'google') }}"
                    class="btn btn-outline-secondary py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/3840px-Google_%22G%22_logo.svg.png"
                        alt="Google Logo" style="width: 20px; height: 20px;">
                    التسجيل بواسطة Google
                </a>

                <!-- GitHub Sign up Button -->
                <a href="{{ route('social.redirect', 'github') }}"
                    class="btn btn-dark py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg"
                        alt="GitHub Logo" style="width: 20px; height: 20px; filter: invert(1);">
                    التسجيل بواسطة GitHub
                </a>
            </div>

            <div class="card p-4 shadow" style="width: 22rem;">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('الاسم') }}</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            placeholder="أدخل اسمك" name="name" value="{{ old('name') }}" required
                            autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('البريد الإلكتروني') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror   text-end"
                            placeholder="أدخل بريدك الإلكتروني" name="email" value="{{ old('email') }}" required
                            autocomplete="email">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('كلمة المرور') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="أدخل كلمة المرور" name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">{{ __('تأكيد كلمة المرور') }}</label>
                        <input id="password-confirm" type="password" class="form-control"
                            placeholder="تأكيد كلمة المرور" name="password_confirmation" required
                            autocomplete="new-password">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        {{ __('إنشاء حساب') }}
                    </button>

                    <!-- Login Link -->
                    <p class="text-center mt-3 mb-0">
                        لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection