@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('تأكيد عنوان البريد الإلكتروني') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('تم إرسال رابط تأكيد جديد إلى عنوان بريدك الإلكتروني.') }}
                        </div>
                    @endif

                    {{ __('قبل المتابعة، يرجى التحقق من بريدك الإلكتروني للحصول على رابط التأكيد.') }}
                    {{ __('إذا لم تتلق البريد الإلكتروني') }}،
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('انقر هنا لطلب رابط آخر') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection