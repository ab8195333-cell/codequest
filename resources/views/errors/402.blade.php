@extends('errors.minimal')

@section('title', __('مطلوب دفع رسوم'))
@section('code', '402')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">عذراً، الوصول إلى هذا المحتوى يتطلب اشتراكاً فعالاً أو ترقية باقتك.</p>
        <a href="{{ url('/') }}" style="display: inline-block; padding: 8px 18px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">ترقية الاشتراك / الرئيسية</a>
    </div>
@endsection