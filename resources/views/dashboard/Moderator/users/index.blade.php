@extends('layouts.dashboard')

@push('css')
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">إدارة المخالفات وحظر المستخدمين</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>نقاط السمعة</th>
                            <th>عدد البلاغات</th>
                            <th>الحالة</th>
                            <th class="text-center">العمليات</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal إرسال تحذير -->
    <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> إرسال تحذير للمستخدم</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="warning_id">
                    <div class="form-group">
                        <label class="font-weight-bold">نص التحذير:</label>
                        <textarea id="warning_message" class="form-group form-control" rows="4"
                            placeholder="اكتب نص التحذير الذي سيظهر للمستخدم في الإشعار..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="button" id="btnSendWarning" class="btn btn-warning">إرسال التحذير</button>
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

            const table = $('#usersTable').DataTable({
                destroy: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
                },
                ajax: {
                    url: "{{ route('users.index') }}",
                    type: "GET",
                    dataSrc: "data"
                },
                columns: [{
                        data: 'id',
                        className: 'text-center align-middle'
                    }, // استبدال user_id بـ id
                    {
                        data: 'name',
                        className: 'align-middle'
                    },
                    {
                        data: 'email',
                        className: 'align-middle'
                    },
                    {
                        data: 'reputation_points',
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'reports_count',
                        className: 'text-center align-middle',
                        render: data => `<span class="badge badge-danger">${data || 0}</span>`
                    },
                    {
                        data: 'is_active',
                        className: 'text-center align-middle',
                        render: function(data) {
                            return data == 1 || data == true ?
                                '<span class="badge badge-success">نشط</span>' :
                                '<span class="badge badge-danger">محظور</span>';
                        }
                    },
                    {
                        data: 'id', // استبدال user_id بـ id
                        orderable: false,
                        className: 'text-center align-middle',
                        render: function(data, type, row) {
                            const banBtnText = (row.is_active == 1 || row.is_active == true) ?
                                'حظر' : 'إلغاء الحظر';
                            const banBtnClass = (row.is_active == 1 || row.is_active == true) ?
                                'btn-danger' : 'btn-success';

                            return `
                <button type="button" class="btn btn-warning btn-sm warn-btn" data-id="${data}">
                    <i class="fas fa-bell"></i> تحذير
                </button>
                <button type="button" class="btn ${banBtnClass} btn-sm ban-btn" data-id="${data}">
                    <i class="fas fa-ban"></i> ${banBtnText}
                </button>
            `;
                        }
                    }
                ]
            });

            // فتح مودال التحذير
            $(document).on('click', '.warn-btn', function() {
                $('#warning_id').val($(this).data('id'));
                $('#warning_message').val('');
                $('#warningModal').modal('show');
            });

            // إرسال التحذير
            $('#btnSendWarning').on('click', function() {
                const userId = $('#warning_id').val();
                const message = $('#warning_message').val();

                if (!message.trim()) {
                    alert('يرجى كتابة نص التحذير.');
                    return;
                }

                axios.post(`{{ url('dashboard/moderator/users') }}/${userId}/warning`, {
                        message: message
                    })
                    .then(res => {
                        $('#warningModal').modal('hide');
                        alert(res.data.message);
                    })
                    .catch(err => alert('حدث خطأ أثناء إرسال التحذير.'));
            });

            // تبديل حالة الحظر
            $(document).on('click', '.ban-btn', function() {
                const userId = $(this).data('id');
                if (confirm('هل أنت تأكد من تغيير حالة حظر هذا المستخدم؟')) {
                    axios.put(`{{ url('dashboard/moderator/users') }}/${userId}/toggle-ban`)
                        .then(res => {
                            table.ajax.reload(null, false);
                            alert(res.data.message);
                        })
                        .catch(err => alert(err.response?.data?.message || 'حدث خطأ أثناء معالجة الطلب.'));
                }
            });
        });
    </script>
@endpush
