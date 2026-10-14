<!-- Flash / JSON Message Modal -->
<div class="modal fade" id="flashMessageModal" tabindex="-1" aria-labelledby="flashMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" id="flashModalHeader">

                <h5 class="modal-title" id="flashMessageModalLabel">

                </h5>
            </div>
            <div class="modal-body fs-6 py-4" id="flashModalBody"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">موافق</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
function showJsonModal(data) {
    var modalElement = document.getElementById('flashMessageModal');
    var header = document.getElementById('flashModalHeader');
    var title = document.getElementById('flashMessageModalLabel');
    var body = document.getElementById('flashModalBody');

    if (!modalElement) return;

    var isSuccess = data.status === 'success' || data.success === true;

    header.className = 'modal-header ' + (isSuccess ? 'bg-success' : 'bg-danger') + ' text-white';
    title.innerHTML = (isSuccess ? '<i class="bi bi-check-circle me-1"></i> ' : '<i class="bi bi-exclamation-triangle me-1"></i> ')
                    + (data.title || (isSuccess ? 'تم الإرسال' : 'تم رفض الطلب'));

    // استخراج نص الرسالة الذكي
    let displayMessage = '';

    // معالجة خطأ تجاوز عدد المحاولات 429 Throttle
    if (data.status === 429) {
        let retryAfter = data.retryAfter ? `يرجى الانتظار لمدة ${data.retryAfter} ثانية ثم المحاولة مجدداً.` : 'يرجى الانتظار قليلاً ثم المحاولة لاحقاً.';
        displayMessage = `لقد تجاوزت الحد الأقصى للمحاولات المسموح بها في الوقت الحالي.<br><br><b>${retryAfter}</b>`;
    } else if (data.errors && typeof data.errors === 'object') {
        // في حال وجود أخطاء Validation مجمعة من لارافل
        displayMessage = Object.values(data.errors).flat().join('<br>');
    } else {
        displayMessage = data.message || data.error || 'حدث خطأ أثناء معالجة الطلب.';
    }

    // استخدام innerHTML لدعم الأسطر المقطعة <br>
    body.innerHTML = displayMessage;

    var flashModal = bootstrap.Modal.getOrCreateInstance(modalElement);
    flashModal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    // تشغيل المودال تلقائياً في حالة الـ Flash Session
    @if (session('success') || session('message_success'))
        showJsonModal({
            success: true,
            message: "{{ session('success') ?? session('message_success') }}"
        });
    @elseif (session('error'))
        showJsonModal({
            success: false,
            message: "{{ session('error') }}"
        });
    @endif
});

// إعداد عام لمعالجة كود 429 لطلبات Axios / Fetch AJAX تلقائياً
if (window.axios) {
    window.axios.interceptors.response.use(
        response => response,
        error => {
            if (error.response && error.response.status === 429) {
                let retryAfter = error.response.headers['retry-after'] || null;
                showJsonModal({
                    status: 429,
                    title: 'تنبيه: تجاوز الحد المسموح',
                    retryAfter: retryAfter
                });
            }
            return Promise.reject(error);
        }
    );
}
    </script>
@endpush
