@extends('errors.minimal')

@section('title', __('خطأ في السيرفر'))
@section('code', '500')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">حدث خطأ غير متوقع في الخادم الداخلي. نعمل على إصلاحه حالياً.</p>
        <a href="{{ url('/') }}" style="display: inline-block; padding: 8px 18px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">الرئيسية</a>
    </div>
@endsection