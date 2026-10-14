@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid py-4">
        <h3 class="mb-4 font-weight-bold text-dark">إعدادات النظام والذكاء الاصطناعي</h3>

        <div class="row">
            <div class="col-md-8">

                <!-- 1. نموذج إعدادات المنصة العامة -->
                <form id="generalSettingsForm" class="mb-4">
                    @csrf
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-gear mr-2 text-primary"></i> إعدادات المنصة العامة</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">اسم المنصة</label>
                                <input type="text" name="site_name" class="form-control"
                                    value="{{ $generalSettings['site_name'] ?? config('app.name') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">البريد الإلكتروني للدعم</label>
                                <input type="email" name="support_email" class="form-control"
                                    value="{{ $generalSettings['support_email'] ?? config('mail.from.address') }}"
                                    aria-describedby="supportEmailHelp">
                                <small id="supportEmailHelp" class="form-text text-muted">
                                    سيتم استخدام هذا البريد الإلكتروني لإرسال رسائل الدعم.
                                </small>
                            </div>
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" class="custom-control-input" id="maintenanceMode"
                                    name="maintenance_mode"
                                    {{ $generalSettings['maintenance_mode'] === '1' ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="maintenanceMode">
                                    تفعيل وضع الصيانة
                                </label>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary px-4">حفظ الإعدادات العامة</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- 2. نموذج إعدادات الذكاء الاصطناعي و n8n -->
                <!-- نموذج إعدادات الذكاء الاصطناعي -->
                <form id="aiSettingsForm" class="mb-4">
                    @csrf
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-robot mr-2 text-info"></i> إعدادات الذكاء الاصطناعي و n8n</span>
                        </div>
                        <div class="card-body">
                            <!-- رابط الـ Webhook -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">رابط n8n Webhook URL</label>
                                <input type="url" name="n8n_webhook_url" class="form-control"
                                    placeholder="https://n8n.yourdomain.com/webhook/..."
                                    value="{{ $aiSettings['n8n_webhook_url'] ?? '' }}">
                            </div>

                            <!-- أمر الـ AI (System Prompt) -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">تعليمات وتوجيهات الذكاء الاصطناعي (System Prompt)</label>
                                <textarea name="system_prompt" class="form-control" rows="5" placeholder="اكتب التوجيهات العامة للنموذج هنا...">{{ $aiSettings['system_prompt'] ?? '' }}</textarea>
                                <small class="form-text text-muted">
                                    هذا النص سيتم إرساله مع كل طلب للـ Webhook لتوجيه أسلوب وطريقة رد المساعد الذكي.
                                </small>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-info text-white px-4">حفظ إعدادات AI</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- قسم النسخ الاحتياطي -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-database-check mr-2 text-success"></i> النسخ الاحتياطي</span>
                        <button id="runBackupBtn" class="btn btn-sm btn-success">
                            <i class="bi bi-cloud-arrow-up"></i> إنشاء نسخة الآن
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if (!empty($backups) && count($backups) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>التاريخ</th>
                                            <th>الحجم</th>
                                            <th class="text-center">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($backups as $backup)
                                            <tr>
                                                <td>{{ $backup['last_modified'] }}</td>
                                                <td><span class="badge badge-secondary">{{ $backup['file_size'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- زر التنزيل -->
                                                    <a href="{{ route('admin.backup.download', $backup['file_name']) }}"
                                                        class="btn btn-sm btn-outline-primary p-1" title="تنزيل">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <!-- زر الحذف -->
                                                    <button onclick="deleteBackup('{{ $backup['file_name'] }}')"
                                                        class="btn btn-sm btn-outline-danger p-1" title="حذف">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted small">
                                لا توجد نسخ احتياطية محفوظة حالياً.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content');

            // 1. حفظ الإعدادات العامة
            document.getElementById('generalSettingsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                axios.post("{{ route('admin.settings.updateGeneral') }}", formData)
                    .then(function(response) {
                        alert(response.data.message);
                    })
                    .catch(function(error) {
                        alert(error.response?.data?.message || 'حدث خطأ أثناء حفظ الإعدادات العامة.');
                    });
            });

            // 2. حفظ إعدادات الذكاء الاصطناعي
            document.getElementById('aiSettingsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                axios.post("{{ route('admin.settings.updateAi') }}", formData)
                    .then(function(response) {
                        alert(response.data.message);
                    })
                    .catch(function(error) {
                        alert(error.response?.data?.message ||
                            'حدث خطأ أثناء حفظ إعدادات الذكاء الاصطناعي.');
                    });
            });

            // 3. تشغيل النسخ الاحتياطي
            document.getElementById('runBackupBtn').addEventListener('click', function() {
                if (!confirm('هل أنت تأكد من بدء إنشاء نسخة احتياطية جديدة الآن؟')) return;

                let btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...';

                axios.post("{{ route('admin.settings.backup') }}")
                    .then(function(response) {
                        alert(response.data.message);
                        location.reload();
                    })
                    .catch(function(error) {
                        alert('حدث خطأ أثناء إجراء النسخ الاحتياطي.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-cloud-arrow-up"></i> إنشاء نسخة الآن';
                    });
            });
        });

        // 4. حذف نسخة احتياطية
        function deleteBackup(fileName) {
            if (!confirm('هل أنت تأكد من حذف هذه النسخة الاحتياطية؟')) return;
            url = "{{ route('admin.backup.delete', ':fileName') }}".replace(':fileName', fileName);
            axios.delete(url)
                .then(function(response) {
                    alert(response.data.message);
                    location.reload();
                })
                .catch(function(error) {
                    alert('حدث خطأ أثناء الحذف.');
                });
        }
    </script>
@endsection
