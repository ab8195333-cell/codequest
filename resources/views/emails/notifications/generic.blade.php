@component('mail::message')
<div dir="rtl" style="text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

# {{ $title }}

مرحباً **{{ $name }}**،

{{ $messageText }}

@if($url)
@component('mail::button', ['url' => url($url), 'color' => 'primary'])
{{ $actionText }}
@endcomponent
@endif

<br>
مع تحيات،<br>
<b>فريق {{ config('app.name') }}</b>
</div>
@endcomponent