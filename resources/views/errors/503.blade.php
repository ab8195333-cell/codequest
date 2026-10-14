@extends('errors.minimal')

@section('title', __('الموقع قيد الصيانة'))
@section('code', '503')
@section('message')
    <div style="direction: rtl;">
        <p style="margin-bottom: 15px;">الموقع خاضع للصيانة الدورية حالياً وسنعود للعمل في أقرب وقت.</p>
    </div>
@endsection