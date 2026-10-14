@extends('errors.minimal')

@section('title', __('وصول ممتنع'))
@section('code', '403')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">عذراً، ليس لديك الصلاحيات الكافية للوصول إلى هذه الصفحة.</p>
        <a href="{{ url('/') }}" style="display: inline-block; padding: 8px 18px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">الرئيسية</a>
    </div>
@endsection