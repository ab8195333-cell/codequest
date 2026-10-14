@extends('layouts.app')

@section('title', 'الإشعارات')

@push('css')
    <style>
        .notification-item {
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .notification-item.unread {
            background-color: #f0f7ff;
        }

        .notification-item:hover {
            background-color: #e9ecef;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5" style="max-width: 800px;">
        <form action="{{ route('profile.notifications.toggle') }}" method="POST" class="algin-items-start mb-4">
            @csrf
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="email_notifications" id="emailSwitch"
                       onchange="this.form.submit()" {{ auth()->user()->email_notifications ? 'checked' : '' }}>
                <label class="form-check-label" for="emailSwitch">تفعيل إشعارات البريد الإلكتروني</label>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">

                <i class="bi bi-bell-fill me-2 text-primary"></i> الإشعارات
                @if ($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill fs-6 ms-2">{{ $unreadCount }}</span>
                @endif
            </h3>

            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-check2-all me-1"></i> تحديد الكل كمقروء
                </button>
            </form>
        </div>

        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">

                @forelse($notifications as $notif)
                <div id="notification-{{ $notif['id'] }}"
                class="list-group-item notification-item {{ !$notif['is_read'] ? 'unread' : '' }} p-3"
                data-bs-toggle="modal"
                data-bs-target="#notificationModal"
                onclick="loadNotification(
                   '{{ $notif['id'] }}',
                   '{{ e($notif['title']) }}',
                   '{{ addslashes($notif['message']) }}',
                   '{{ $notif['time'] }}',
                   '{{ $notif['url'] }}',
                   {{ $notif['is_read'] ? 'true' : 'false' }}
                )">
                    <div class="d-flex align-items-start">
                        <div class="fs-4 {{ $notif['icon_color'] }} me-3">
                            <i class="bi {{ $notif['icon_class'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1 {{ !$notif['is_read'] ? 'fw-bold' : 'fw-normal' }} text-dark">{{ $notif['title'] }}</h6>
                                <small class="text-muted">{{ $notif['time'] }}</small>
                            </div>
                            <p class="mb-1 text-secondary small">{{ $notif['message'] }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
                    لا يوجد إشعارات حالياً.
                </div>
            @endforelse

            </div>
        </div>
    </div>

    <!-- Modal تفاصيل الإشعار -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="notifTitle">عنوان الإشعار</h5>
                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p id="notifBody" class="text-secondary fs-6 mb-3">نص الإشعار التفصيلي يظهر هنا...</p>
                    <div class="text-start">
                        <small id="notifTime" class="text-muted">التاريخ والوقت</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                    <a href="#" id="notifActionLink" class="btn btn-primary btn-sm">الانتقال للرابط</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
function loadNotification(id, title, message, time, actionUrl, isRead) {
    // 1. تعبئة البيانات داخل Modal تفاصيل الإشعار
    document.getElementById('notifTitle').innerText = title;
    document.getElementById('notifBody').innerText = message;
    document.getElementById('notifTime').innerText = time;

    const actionBtn = document.getElementById('notifActionLink');
    if (actionUrl && actionUrl !== '#') {
        actionBtn.href = actionUrl;
        actionBtn.style.display = 'inline-block';
    } else {
        actionBtn.style.display = 'none';
    }

    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '{{ csrf_token() }}';

    // 2. تحديث حالة الإشعار باستخدام مسار Laravel الديناميكي لضمان عدم حدوث 404
    if (!isRead) {
        fetch(`{{ url('/notification') }}/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
    if (data.success) {
        // 1. إزالة كلاس غير المقروء من بطاقة الإشعار
        const notifElement = document.getElementById(`notification-${id}`);
        if (notifElement) {
            notifElement.classList.remove('unread');
        }

        // 2. تحديث قيمة isRead للمرة القادمة حتى لا يرسل طلب Fetch مكرر
        isRead = true;
    }})
        .catch(error => {
            console.error('Error:', error);
            showJsonModal({
                success: false,
                title: 'خطأ بالشبكة',
                message: 'حدث خطأ أثناء محاولة تحديث الإشعار، يرجى المحاولة لاحقاً.'
            });
        });
    }
}
    </script>
@endpush
