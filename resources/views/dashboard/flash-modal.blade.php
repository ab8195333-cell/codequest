<!-- Flash / JSON Message Modal -->
<div class="modal fade" id="flashMessageModal" tabindex="-1" role="dialog" aria-labelledby="flashMessageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" id="flashModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title " id="flashMessageModalLabel"></h5>
            </div>
            <div class="modal-body py-4" id="flashModalBody"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">موافق</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function showJsonModal(data) {
            var $modal = $('#flashMessageModal');
            var $header = $('#flashModalHeader');
            var $title = $('#flashMessageModalLabel');
            var $body = $('#flashModalBody');

            if (!$modal.length) return;

            var isSuccess = data.status === 'success' || data.success === true;

            // ضبط لون الترويسة والأيقونة باستخدام FontAwesome الخاص بـ Bootstrap 4
            $header.attr('class', 'modal-header ' + (isSuccess ? 'bg-success' : 'bg-danger') + ' text-white');

            var icon = isSuccess ? '<i class="fas fa-check-circle mr-1"></i> ' :
                '<i class="fas fa-exclamation-triangle mr-1"></i> ';
            var defaultTitle = isSuccess ? 'تم الإرسال' : 'تم رفض الطلب';

            $title.html(icon + (data.title || defaultTitle));

            // استخراج نص الرسالة الذكي
            let displayMessage = '';

            if (data.status === 429) {
                let retryAfter = data.retryAfter ? `يرجى الانتظار لمدة ${data.retryAfter} ثانية ثم المحاولة مجدداً.` :
                    'يرجى الانتظار قليلاً ثم المحاولة لاحقاً.';
                displayMessage =
                    `لقد تجاوزت الحد الأقصى للمحاولات المسموح بها في الوقت الحالي.<br><br><b>${retryAfter}</b>`;
            } else if (data.errors && typeof data.errors === 'object') {
                displayMessage = Object.values(data.errors).flat().join('<br>');
            } else {
                displayMessage = data.message || data.error || 'حدث خطأ أثناء معالجة الطلب.';
            }

            $body.html(displayMessage);

            // تشغيل المودال عبر jQuery الخاص بـ Bootstrap 4
            $modal.modal('show');
        }

        $(document).ready(function() {
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
            @elseif (session('warning'))
                showJsonModal({
                    status: 'warning',
                    message: "{{ session('warning') }}"
                });
            @elseif ($errors->any())
                showJsonModal({
                    success: false,
                    title: 'خطأ في المدخلات',
                    errors: {!! json_encode($errors->all()) !!}
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
