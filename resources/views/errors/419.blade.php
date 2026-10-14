@extends('errors.minimal')

@section('title', __('انتهت صلاحية الجلسة'))
@section('code', '419')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">انتهت صلاحية الجلسة بسبب عدم النشاط. يرجى إعادة التحميل.</p>
        <button onclick="window.location.reload()" style="padding: 8px 18px; background-color: #0d6efd; color: #fff; border: none; border-radius: 5px; font-size: 14px; cursor: pointer;">تحديث الصفحة</button>
    </div>
@endsection