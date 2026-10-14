@extends('errors.minimal')

@section('title', __('غير مصرح'))
@section('code', '401')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">يجب تسجيل الدخول أولاً للوصول إلى هذه الصفحة.</p>
        <a href="{{ route('login') }}" style="display: inline-block; padding: 8px 18px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">تسجيل الدخول</a>
    </div>
@endsection