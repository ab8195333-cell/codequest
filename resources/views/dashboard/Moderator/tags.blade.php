@extends('layouts.dashboard')
@push('css')
    <!-- التنسيقات الخاصة بهذه الصفحة -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <style>
        table.dataTable thead th:first-child::before,
        table.dataTable thead th:first-child::after {
            display: none !important;
            content: "" !important;
        }
    </style>
@endpush

@section('content')
    <!-- بطاقة نموذج إنشاء وسم جديد -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">إضافة وسم جديد</h6>
        </div>
        <div class="card-body">
            <form id="FormCreate">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="font-weight-bold">اسم الوسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="أدخل اسم الوسم" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="description" class="font-weight-bold">الوصف</label>
                        <input type="text" name="description" id="description"
                            class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                            placeholder="أدخل وصفاً قصيراً">
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm text-white-50"></i> حفظ الوسم
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول الوسوم -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جدول الوسوم</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="3%" class="text-center">
                                <input type="checkbox" id="selectAllHeader">
                            </th>
                            <th>#</th>
                            <th>ID القاعدة</th>
                            <th>اسم الوسم</th>
                            <th>الوصف</th>
                            <th>تاريخ التحديث</th>
                            <th>تاريخ الإنشاء</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- سيتم بناء الصفوف تلقائياً عبر DataTables AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- أزرار العمليات المجمعة أسفل الجدول داخل الكارد -->
            <div class="mt-3 pt-3 border-top d-flex align-items-center">
                <button type="button" id="btnSelectAll" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-check-square"></i> تحديد الكل
                </button>
                <button type="button" id="btnEditSelected" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-edit"></i> تعديل
                </button>
                <button type="button" id="btnDeleteSelected" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> حذف المحدد
                </button>
            </div>
        </div>
    </div>

    <!-- نافذة التعديل المفرد -->
    <div class="modal fade" id="edittagModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل الوسم</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edittagForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id">
                        <div class="form-group">
                            <label>اسم الوسم</label>
                            <input type="text" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>الوصف</label>
                            <textarea id="edit_description" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة التعديل الجماعي -->
    <div class="modal fade" id="bulkEditModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل العناصر المحددة</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="bulkEditForm">
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>اسم الوسم</th>
                                    <th>الوصف</th>
                                </tr>
                            </thead>
                            <tbody id="bulkEditTableBody">
                                <!-- يتم جلب الخانات والبيانات تلقائياً عبر JS -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ جميع التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal تأكيد الحذف -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="deleteConfirmMessage">
                    <!-- سيتم كتابة نص التنبيه هنا عبر JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">حذف</button>
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

            // =========================================================================
            // 0. إعداد الترويسات الافتراضية لـ Axios (CSRF Token & AJAX Header)
            // =========================================================================
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';


            // =========================================================================
            // 1. تهيئة جدول البيانات DataTables
            // =========================================================================
            const table = $('#dataTable').DataTable({

                destroy: true,
                responsive: true,
                order: [
                    [2, 'desc']
                ], // الترتيب حسب العمود رقم 2 (ID القاعدة) تنازلياً من الأحدث للأقدم
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json"
                },
                ajax: {
                    url: "{{ route('tags.index') }}",
                    type: "GET",
                    dataSrc: ""
                },
                // بقية الأعمدة كما هي لديك...
                columns: [{
                        // العمود 0: مربع تحديد الصف (Checkbox)
                        data: 'tag_id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle',
                        render: function(data) {
                            return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                        }
                    },
                    {
                        // العمود 1: التسلسل التلقائي للصفوف
                        data: null,
                        title: "#",
                        className: 'text-center align-middle',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        // العمود 2: معرف القاعدة
                        data: 'tag_id',
                        title: "ID القاعدة",
                        searchable: true,
                        orderable: true,
                        className: 'text-center align-middle'
                    },
                    {
                        // العمود 3: اسم الوسم
                        data: 'name',
                        title: "اسم الوسم",
                        className: 'align-middle'
                    },
                    {
                        // العمود 4: الوصف
                        data: 'description',
                        title: "الوصف",
                        className: 'align-middle',
                        render: function(data) {
                            return data ? data : 'غير متوفر';
                        }
                    },
                    {
                        // العمود 5: تاريخ التعديل
                        data: 'updated_at',
                        title: "تاريخ التحديث",
                        className: 'align-middle',
                        render: function(data) {
                            return data ? data.substring(0, 10) : 'غير متوفر';
                        }
                    },
                    {
                        // العمود 6: تاريخ الإنشاء
                        data: 'created_at',
                        title: "تاريخ الإنشاء",
                        className: 'align-middle',
                        render: function(data) {
                            return data ? data.substring(0, 10) : 'غير متوفر';
                        }
                    },
                    {
                        // العمود 7: أزرار العمليات (تعديل مفرد / حذف مفرد)
                        data: 'tag_id',
                        title: "العمليات",
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle',
                        render: function(data, type, row) {
                            return `
                                <button type="button"
                                        class="btn btn-primary btn-circle btn-sm edit-btn"
                                        data-tag_id="${data}"
                                        data-name="${row.name}"
                                        data-description="${row.description || ''}"
                                        data-toggle="modal"
                                        data-target="#edittagModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-circle btn-sm delete-btn" data-tag_id="${data}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });


            // =========================================================================
            // 2. إدارة التعديل المفرد (Single Edit)
            // =========================================================================

            // أ. تعبئة حقول النموذج بالبيانات عند فتح النافذة المنبثقة
            $(document).on('click', '.edit-btn', function() {
                $('#edit_id').val($(this).data('tag_id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_description').val($(this).data('description'));
            });

            // ب. إرسال بيانات التحديث المفرد عبر Axios
            $('#edittagForm').off('submit').on('submit', function(e) {
                e.preventDefault();

                const id = $('#edit_id').val();
                const updateUrl = "{{ route('tags.update', ':id') }}".replace(':id', id);

                axios.put(updateUrl, {
                        name: $('#edit_name').val(),
                        description: $('#edit_description').val()
                    })
                    .then(function(response) {
                        if ($('#edittagModal').length) {
                            $('#edittagModal').modal('hide');
                        }

                        table.ajax.reload(null, false);

                        if (typeof showJsonModal === 'function') {
                            showJsonModal({
                                status: 'success',
                                title: 'تم التحديث',
                                message: response.data.message || 'تم تحديث الوسم بنجاح.'
                            });
                        }
                    })
                    .catch(function(error) {
                        let errorMessage = 'حدث خطأ غير متوقع أثناء التحديث.';

                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors;
                            errorMessage = errors ? Object.values(errors).flat().join('<br>') : error
                                .response.data.message;
                        } else if (error.response && error.response.data && error.response.data
                            .message) {
                            errorMessage = error.response.data.message;
                        }

                        if (typeof showJsonModal === 'function') {
                            showJsonModal({
                                status: 'error',
                                title: 'فشل التحديث',
                                message: errorMessage
                            });
                        } else {
                            alert(errorMessage);
                        }
                    });
            });


            // =========================================================================
            // 3. إدارة التحديد التلقائي للصفوف (Checkboxes)
            // =========================================================================

            // أ. تحديد / إلغاء تحديد الكل
            $('#selectAllHeader, #btnSelectAll').on('click', function(e) {
                if (e.target.id === 'btnSelectAll') {
                    const currentHeaderState = $('#selectAllHeader').prop('checked');
                    $('#selectAllHeader').prop('checked', !currentHeaderState);
                }
                const isChecked = $('#selectAllHeader').prop('checked');
                $('.row-checkbox').prop('checked', isChecked);
            });

            // ب. مزامنة مربع تحديد الكل عند تغيير حالة أي مربع فردي
            $(document).on('change', '.row-checkbox', function() {
                if (!$(this).prop('checked')) {
                    $('#selectAllHeader').prop('checked', false);
                } else if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
                    $('#selectAllHeader').prop('checked', true);
                }
            });

            // ج. دالة جلب المعرفات المحددة
            function getSelectedIds() {
                let selectedIds = [];
                $('.row-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });
                return selectedIds;
            }


            // =========================================================================
            // 4. إدارة عملية الحذف (Bulk & Single Delete) باستخدام Modal
            // =========================================================================

            let deleteTargetUrl = '';
            let deleteTargetIds = [];

            // أ. تجهيز فتح المودال للحذف الجماعي (Bulk Delete)
            $('#btnDeleteSelected').on('click', function() {
                deleteTargetIds = getSelectedIds();

                if (deleteTargetIds.length === 0) {
                    if (typeof showJsonModal === 'function') {
                        showJsonModal({
                            status: 'warning',
                            title: 'تنبيه',
                            message: 'يرجى تحديد عنصر واحد على الأقل للحذف.'
                        });
                    } else {
                        alert('يرجى تحديد عنصر واحد على الأقل للحذف.');
                    }
                    return;
                }

                deleteTargetUrl = "{{ route('tags.destroy', ':id') }}".replace(':id', deleteTargetIds.join(
                    ','));
                $('#deleteConfirmMessage').text(
                    `هل أنت متأكد من حذف ${deleteTargetIds.length} عنصر/عناصر؟`);
                $('#deleteConfirmModal').modal('show');
            });

            // ب. تجهيز فتح المودال للحذف المفرد (Single Delete)
            $(document).on('click', '.delete-btn', function() {
                const tagId = $(this).data('tag_id');
                deleteTargetIds = [tagId];
                deleteTargetUrl = "{{ route('tags.destroy', ':id') }}".replace(':id', tagId);

                $('#deleteConfirmMessage').text('هل أنت متأكد من حذف هذا العنصر؟');
                $('#deleteConfirmModal').modal('show');
            });

            // ج. تنفيذ الحذف عند الضغط على زر التأكيد داخل الـ Modal
            $('#confirmDeleteBtn').off('click').on('click', function() {
                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الحذف...');

                axios.delete(deleteTargetUrl)
                    .then(function(response) {
                        $('#deleteConfirmModal').modal('hide');
                        $('#selectAllHeader').prop('checked', false);
                        table.ajax.reload(null, false);

                        if (typeof showJsonModal === 'function') {
                            showJsonModal({
                                status: 'success',
                                title: 'تم الحذف',
                                message: response.data.message || 'تم حذف العناصر بنجاح.'
                            });
                        }
                    })
                    .catch(function(error) {
                        console.error('Error deleting tags:', error);
                        $('#deleteConfirmModal').modal('hide');

                        if (typeof showJsonModal === 'function') {
                            showJsonModal({
                                status: 'error',
                                title: 'فشل الحذف',
                                message: 'حدث خطأ أثناء تنفيذ عملية الحذف.' + error
                            });
                        } else {
                            alert('حدث خطأ أثناء تنفيذ عملية الحذف.');
                        }
                    })
                    .finally(function() {
                        $btn.prop('disabled', false).text('حذف');
                    });
            });


            // =========================================================================
            // 5. إنشاء وسم جديد (Create tag)
            // =========================================================================
            $('#FormCreate').off('submit').on('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);

                axios.post('{{ route('tags.store') }}', formData)
                    .then(response => {
                        this.reset();
                        table.ajax.reload(null, false);

                        if (typeof showJsonModal === 'function') {
                            showJsonModal({
                                status: 'success',
                                title: 'تم الحفظ',
                                message: response.data.message || 'تم إنشاء الوسم بنجاح.'
                            });
                        }
                    })
                    .catch(error => {
                        let errorMessage = 'حدث خطأ أثناء معالجة الطلب.';

                        if (error.response && error.response.data && error.response.data.errors) {
                            const errors = error.response.data.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }

                        if (typeof showJsonModal === 'function') {
                            showJsonModal({
                                status: 'warning',
                                title: 'تنبيه',
                                message: errorMessage
                            });
                        }
                    });
            });


            // =========================================================================
            // 6. إدارة التعديل الجماعي (Bulk Edit)
            // =========================================================================

            // أ. تجهيز وفتح نافذة التعديل الجماعي (Modal)
            $('#btnEditSelected').on('click', function() {
                const selectedRows = $('.row-checkbox:checked').closest('tr');

                if (selectedRows.length === 0) {
                    alert('يرجى تحديد عنصر واحد على الأقل للتعديل.');
                    return;
                }

                let rowsHtml = '';

                selectedRows.each(function() {
                    const rowData = table.row($(this)).data();
                    const id = rowData.tag_id;
                    const name = rowData.name || '';
                    const description = (rowData.description && rowData.description !== 'N/A' &&
                        rowData.description !== 'غير متوفر') ? rowData.description : '';

                    rowsHtml += `
                        <tr data-tag_id="${id}">
                            <td class="text-center align-middle">
                                <span class="badge badge-secondary">${id}</span>
                            </td>
                            <td>
                                <input type="text" class="form-control edit-name" value="${name}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control edit-description" value="${description}">
                            </td>
                        </tr>
                    `;
                });

                $('#bulkEditTableBody').html(rowsHtml);
                $('#bulkEditModal').modal('show');
            });

            // ب. حفظ التعديلات الجماعية المرسلة من الـ Modal
            $('#bulkEditForm').off('submit').on('submit', function(e) {
                e.preventDefault();

                let tagsData = [];

                $('#bulkEditTableBody tr').each(function() {
                    tagsData.push({
                        tag_id: $(this).data('tag_id'),
                        name: $(this).find('.edit-name').val(),
                        description: $(this).find('.edit-description').val()
                    });
                });

                axios.put("{{ route('tags.update', 'bulk') }}", {
                        tags: tagsData
                    })
                    .then(function(response) {
                        $('#bulkEditModal').modal('hide');
                        table.ajax.reload(null, false);
                        $('#selectAllHeader').prop('checked', false);

                        if (typeof showJsonModal === 'function') {
                            showJsonModal({
                                status: 'success',
                                title: 'تم التحديث',
                                message: response.data.message || 'تم تحديث العناصر بنجاح.'
                            });
                        }
                    })
                    .catch(function(error) {
                        alert('حدث خطأ أثناء حفظ التعديلات.');
                    });
            });

        });
    </script>
@endpush
