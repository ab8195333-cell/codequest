@extends('layouts.dashboard')

@push('css')
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">إدارة البلاغات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reportsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="3%" class="text-center">
                                <input type="checkbox" id="selectAllHeader">
                            </th>
                            <th>#</th>
                            <th>ID القاعدة</th>
                            <th>المُبلّغ</th>
                            <th>نوع المحتوى</th>
                            <th>سبب البلاغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th class="text-center">العمليات</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mt-3 pt-3 border-top">
                <button type="button" id="btnDeleteSelected" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> حذف المحدد
                </button>
            </div>
        </div>
    </div>

    <!-- Modal معاينة لاتخاذ الإجراء -->
    <div class="modal fade" id="actionReportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تفاصيل البلاغ والمحتوى</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="report_id">
                    <div class="form-group">
                        <label class="font-weight-bold">السبب المذكور:</label>
                        <p id="report_reason" class="p-2 bg-light border rounded"></p>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">المحتوى المُبلغ عنه:</label>
                        <div id="reportable_content" class="p-3 bg-light border rounded"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between flex-wrap">
                    <!-- أزرار الإجراءات على البلاغ والمحتوى -->
                    <div class="d-flex flex-wrap gap-2">
                        <!-- 1. زر قبول البلاغ (بدون حذف المحتوى - للمحتوى الذي فيه غلط بسيط) -->
                        <button type="button" class="btn btn-success" onclick="handleReport('resolved', false)">
                            <i class="fas fa-check-circle"></i> قبول البلاغ
                        </button>

                        <!-- 2. زر حذف المحتوى وقبول البلاغ -->
                        <button type="button" class="btn btn-danger" onclick="handleReport('resolved', true)">
                            <i class="fas fa-trash-alt"></i> حذف المحتوى وقبول البلاغ
                        </button>

                        <!-- 3. زر تجاهل البلاغ -->
                        <button type="button" class="btn btn-warning text-white"
                            onclick="handleReport('dismissed', false)">
                            <i class="fas fa-ban"></i> تجاهل البلاغ
                        </button>
                    </div>

                    <!-- أزرار التنقل والإغلاق -->
                    <div>
                        <a id="viewQuestionBtn" href="#" target="_blank" class="btn btn-info">
                            <i class="fas fa-external-link-alt"></i> الذهاب إلى المحتوى
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal تأكيد الحذف الجماعي -->
    <div class="modal fade" id="deleteBulkConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-left-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> تأكيد الحذف الجماعي</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    هل أنت تأكد من رغبتك في حذف <strong id="selectedCountText" class="text-danger">0</strong> من البلاغات
                    المحددة؟ لا يمكن التراجع عن هذه العملية.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="button" id="confirmBulkDeleteBtn" class="btn btn-danger">تأكيد الحذف</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal التنبيه (بديل alert) -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" id="alertModalHeader">
                    <h5 class="modal-title" id="alertModalTitle">تنبيه</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="alertModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">موافق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

            const table = $('#reportsTable').DataTable({
                destroy: true,
                responsive: true,
                order: [
                    [6, 'desc']
                ], // ترتيب حسب التاريخ
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
                },
                ajax: {
                    url: "{{ route('reports.index') }}", // أو مسار الـ URL المباشر: '/dashboard/moderator/reports'
                    type: "GET",
                    dataSrc: "data"
                },
                columns: [{
                        data: 'report_id',
                        orderable: false,
                        className: 'text-center align-middle',
                        render: function(data) {
                            return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center align-middle',
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'report_id',
                        title: 'ID القاعدة'
                    },
                    {
                        data: 'user',
                        className: 'align-middle',
                        render: function(data) {
                            return data ? data.name : 'مستخدم غير معروف';
                        }
                    },
                    {
                        data: 'reportable_type',
                        className: 'align-middle',
                        render: function(data) {
                            if (!data) return 'غير محدد';
                            const type = data.split('\\').pop();
                            return `<span class="badge badge-info">${type}</span>`;
                        }
                    },
                    {
                        data: 'reason',
                        className: 'align-middle'
                    },
                    {
                        data: 'status',
                        className: 'text-center align-middle',
                        render: function(data) {
                            if (data === 'pending')
                                return '<span class="badge badge-warning">معلق</span>';
                            if (data === 'resolved')
                                return '<span class="badge badge-success">مقبول</span>';
                            return '<span class="badge badge-secondary">مرفوض/متجاهل</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        className: 'align-middle',
                        render: data => data ? data.substring(0, 10) : ''
                    },
                    // بداخل تعريف الأعمدة columns في DataTables:
                    {
                        data: 'report_id',
                        orderable: false,
                        className: 'text-center align-middle',
                        render: function(data, type, row) {
                            // تحديد معرّف السؤال بناءً على نوع المحتوى المُبلغ عنه
                            let questionId = null;
                            if (row.reportable_type && row.reportable_type.includes('Question')) {
                                questionId = row.reportable_id;
                            } else if (row.reportable && row.reportable.question_id) {
                                questionId = row.reportable.question_id;
                            }

                            return `
            <button type="button" class="btn btn-primary btn-sm view-btn"
                data-id="${data}"
                data-reason="${row.reason}"
                data-question_id="${questionId || ''}"
                data-content='${JSON.stringify(row.reportable || {})}'>
                <i class="fas fa-eye"></i> معاينة
            </button>
        `;
                        }
                    }
                ]
            });

            // فتح مودال المعاينة
            $(document).on('click', '.view-btn', function() {
                const id = $(this).data('id');
                const reason = $(this).data('reason');
                const contentObj = $(this).data('content');
                const questionId = $(this).data('question_id');

                $('#report_id').val(id);
                $('#report_reason').text(reason);

                let contentHtml = contentObj.title || contentObj.content || contentObj.body ||
                    'لا يوجد نص للمعاينة أو تم حذفه سابقاً.';
                $('#reportable_content').html(contentHtml);

                // ضبط رابط زر الانتقال للسؤال
                const $qBtn = $('#viewQuestionBtn');
                if (questionId) {
                    // تفترض هذه الصياغة أن اسم مسار عرض السؤال هو questions.show
                    const questionUrl = "{{ route('questions.show', ':id') }}".replace(':id', questionId);
                    $qBtn.attr('href', questionUrl).show();
                } else {
                    // إخفاء الزر في حال كان السؤال غير متوفر أو محذوف
                    $qBtn.hide();
                }

                $('#actionReportModal').modal('show');
            });
        });

        // دالة التعامل مع البلاغ
        function handleReport(status, deleteContent) {
            const id = $('#report_id').val();
            const url = "{{ route('reports.updateStatus', ':id') }}".replace(':id', id);

            axios.put(url, {
                    status: status,
                    delete_content: deleteContent
                })
                .then(response => {
                    $('#actionReportModal').modal('hide');
                    $('#reportsTable').DataTable().ajax.reload(null, false);
                    alert(response.data.message);
                })
                .catch(error => {
                    alert('حدث خطأ أثناء معالجة البلاغ.');
                });
        }
        $(document).on('click', '#selectAllHeader', function() {
            const isChecked = $(this).prop('checked');
            $('.row-checkbox').prop('checked', isChecked);
        });

        // 2. عند تغيير تحديد أي مربع فردي، يتم فحص هل تم تحديد كل العناصر أم لا
        $(document).on('change', '.row-checkbox', function() {
            const totalCheckboxes = $('.row-checkbox').length;
            const checkedCheckboxes = $('.row-checkbox:checked').length;

            // تحديد المربع الرئيسي فقط إذا كانت كل المربعات الفردية محددة
            $('#selectAllHeader').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        });
        // دالة مخصصة لإظهار المودال بدلاً من alert العادية
        function showAlert(message, type = 'info') {
            const $header = $('#alertModalHeader');
            $header.removeClass('bg-success bg-danger bg-warning text-white');

            if (type === 'success') {
                $header.addClass('bg-success text-white');
                $('#alertModalTitle').text('نجاح');
            } else if (type === 'danger') {
                $header.addClass('bg-danger text-white');
                $('#alertModalTitle').text('خطأ');
            } else {
                $header.addClass('bg-warning text-white');
                $('#alertModalTitle').text('تنبيه');
            }

            $('#alertModalBody').text(message);
            $('#alertModal').modal('show');
        }

        // متغير لتخزين المعرفات المحددة
        let selectedReportIds = [];

        // عند الضغط على زر "حذف المحدد"
        $('#btnDeleteSelected').on('click', function() {
            selectedReportIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedReportIds.push($(this).val());
            });

            if (selectedReportIds.length === 0) {
                showAlert('يرجى تحديد بلاغ واحد على الأقل للحذف.', 'warning');
                return;
            }

            // إظهار عدد العناصر في المودال ثم فتح المودال
            $('#selectedCountText').text(selectedReportIds.length);
            $('#deleteBulkConfirmModal').modal('show');
        });

        // عند تأكيد الحذف بداخل المودال
        $('#confirmBulkDeleteBtn').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحذف...');

            const idsString = selectedReportIds.join(',');
            const url = "{{ route('reports.destroy', ':id') }}".replace(':id', idsString);

            axios.delete(url)
                .then(function(response) {
                    $('#deleteBulkConfirmModal').modal('hide');
                    $('#reportsTable').DataTable().ajax.reload(null, false);
                    $('#selectAllHeader').prop('checked', false);

                    showAlert(response.data.message || 'تم حذف البلاغات المحددة بنجاح.', 'success');
                })
                .catch(function(error) {
                    $('#deleteBulkConfirmModal').modal('hide');
                    showAlert('حدث خطأ أثناء تنفيذ عملية الحذف الجماعي.', 'danger');
                })
                .then(function() {
                    $btn.prop('disabled', false).text('تأكيد الحذف');
                });
        });
    </script>
@endpush
