@extends('layouts.dashboard')
@push('css')
    <!-- الملحقات الخاصة بالصفحة -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid py-4">
        <h3 class="mb-4 font-weight-bold text-dark">إدارة الأدوار والصلاحيات</h3>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="adminUsersTable" class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الصلاحية الحالية</th>
                                <th>الحالة</th>
                                <th>تاريخ الانضمام</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- الملحقات الخاصة بالصفحة -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // إعداد Axios لإرسال CSRF Token تلقائياً في جميع الطلبات
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content');
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

            let table = $('#adminUsersTable').DataTable({

                processing: true,
                ajax: {
                    url: "{{ route('users.index') }}",
                    dataSrc: 'data'
                },
                columns: [{
                        searchable: true,
                        orderable: true,

                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        searchable: true,
                        orderable: true,
                        data: 'role',
                        render: function(data, type, row) {
                            return `
                        <select class="form-control form-control-sm role-select" data-id="${row.id}">
                            <option value="user" ${data === 'user' ? 'selected' : ''}>مستخدم عادي</option>
                            <option value="moderator" ${data === 'moderator' ? 'selected' : ''}>مشرف (Moderator)</option>
                            <option value="admin" ${data === 'admin' ? 'selected' : ''}>مدير (Admin)</option>
                        </select>
                    `;
                        }
                    },
                    {
                        data: 'is_active',
                        render: function(data) {
                            return data ?
                                '<span class="badge badge-success">نشط</span>' :
                                '<span class="badge badge-danger">محظور</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('ar-EG');
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let btnText = row.is_active ? 'حظر' : 'تفعيل';
                            let btnClass = row.is_active ? 'btn-outline-danger' :
                                'btn-outline-success';
                            return `<button class="btn btn-sm ${btnClass} toggle-status-btn" data-id="${row.id}">${btnText}</button>`;
                        },
                        orderable: false
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
                }
            });

            // 1. تغيير الصلاحية باستخدام Axios
            $(document).on('change', '.role-select', function() {
                let userId = $(this).data('id');
                let newRole = $(this).val();
                const url = "{{ route('admin.users.update-role', ':id') }}".replace(':id', userId);
                axios.patch(url, {
                        role: newRole
                    })
                    .then(function(response) {
                        alert(response.data.message);
                        table.ajax.reload(null, false);
                    })
                    .catch(function(error) {
                        let errorMsg = 'حدث خطأ أثناء تغيير الصلاحية.';

                        if (error.response && error.response.data && error.response.data.message) {
                            errorMsg = error.response.data.message;
                        }

                        alert(errorMsg);
                        table.ajax.reload(null, false);
                    });
            });

            // 2. تفعيل / حظر المستخدم باستخدام Axios
            $(document).on('click', '.toggle-status-btn', function() {
                let userId = $(this).data('id');

                axios.patch("{{ route('admin.users.toggle-status', ':id') }}".replace(':id', userId))
                    .then(function(response) {
                        alert(response.data.message);
                        table.ajax.reload(null, false);
                    })
                    .catch(function(error) {
                        let errorMsg = 'حدث خطأ أثناء تغيير حالة المستخدم.';

                        if (error.response && error.response.data && error.response.data.message) {
                            errorMsg = error.response.data.message;
                        }

                        alert(errorMsg);
                        table.ajax.reload(null, false);
                    });
            });
        });
    </script>
@endpush
