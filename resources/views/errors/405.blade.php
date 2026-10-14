@extends('errors.minimal')

@section('title', __('طريقة الطلب غير مسموح بها'))
@section('code', '405')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">عذراً، نوع الطلب استخدم طريقة غير مسموح بها للوصول إلى هذا المسار.</p>
        <a href="{{ url('/') }}" style="display: inline-block; padding: 8px 18px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">العودة للرئيسية</a>
    </div>
@endsection