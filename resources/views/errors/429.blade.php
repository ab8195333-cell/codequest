@extends('errors.minimal')

@section('title', __('تجاوز حد الطلبات'))
@section('code', '429')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">لقد قمت بإجراء طلبات كثيرة جداً في وقت قصير. يرجى الانتظار دقيقة واحدة ثم المحاولة.</p>
        <button onclick="window.history.back()" style="padding: 8px 18px; background-color: #6c757d; color: #fff; border: none; border-radius: 5px; font-size: 14px; cursor: pointer;">العودة للخلف</button>
    </div>
@endsection