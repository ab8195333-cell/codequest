@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid py-2">

        <!-- الترحيب ورأس الصفحة -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">لوحة التحكم والتنقل</h1>
                <p class="text-muted small mb-0">مرحباً بك، {{ auth()->user()->name }}! استخدم الإختصارات أدناه للوصول السريع
                    إلى أقسام اللوحة.</p>
            </div>
            <div>
                <a href="{{ route('index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-right me-1"></i> العودة للرئيسية
                </a>
            </div>
        </div>

        <!-- 1. أجنحة الإدارة الخاصة بالـ Admin فقط -->
        @if (auth()->user()->role === 'admin')
            <h5 class="fw-bold text-primary mb-3">
                <i class="fas fa-user-shield me-1"></i> أدوات المدير (Admin)
            </h5>
            <div class="row g-3 mb-4">

                <!-- بطاقة الإحصائيات التحليلية -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('admin.index') }}" class="text-decoration-none">
                        <div
                            class="card border-0 shadow-sm h-100 border-start border-primary border-4 hover-shadow transition">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">التحليلات</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">الإحصائيات التحليلية</div>
                                    <small class="text-muted">مراقبة أداء المنصة والنشاط</small>
                                </div>
                                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- بطاقة إعدادات النظام والـ AI -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('admin.settings.index') }}" class="text-decoration-none">
                        <div
                            class="card border-0 shadow-sm h-100 border-start border-success border-4 hover-shadow transition">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">النظام & AI</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">إعدادات الـ AI والمنصة</div>
                                    <small class="text-muted">مفاتيح API ونماذج الذكاء الاصطناعي</small>
                                </div>
                                <div class="bg-success-subtle text-success p-3 rounded-circle">
                                    <i class="fas fa-cogs fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- بطاقة أدوار المستخدمين -->
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                        <div
                            class="card border-0 shadow-sm h-100 border-start border-info border-4 hover-shadow transition">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">الصلاحيات</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">أدوار المستخدمين</div>
                                    <small class="text-muted">إدارة المشرفين وصلاحيات الحسابات</small>
                                </div>
                                <div class="bg-info-subtle text-info p-3 rounded-circle">
                                    <i class="fas fa-user-shield fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        @endif

        <!-- 2. أدوات الإشراف (المشرفين والمدراء) -->
        <h5 class="fw-bold text-secondary mb-3">
            <i class="fas fa-tasks me-1"></i> أدوات الإشراف والمحتوى
        </h5>
        <div class="row g-3 mb-4">

            <!-- إدارة البلاغات -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('reports.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 border-start border-warning border-4 hover-shadow transition">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">المراجعة</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">إدارة البلاغات</div>
                            </div>
                            <div class="bg-warning-subtle text-warning p-2.5 rounded-circle">
                                <i class="fas fa-flag fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- رقابة المستخدمين -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('users.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 border-start border-danger border-4 hover-shadow transition">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">الحظر والرقابة</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">رقابة المستخدمين</div>
                            </div>
                            <div class="bg-danger-subtle text-danger p-2.5 rounded-circle">
                                <i class="fas fa-user-slash fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- إدارة التصنيفات -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('categories.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 border-start border-dark border-4 hover-shadow transition">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">الهيكلة</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">التصنيفات</div>
                            </div>
                            <div class="bg-light text-dark p-2.5 rounded-circle border">
                                <i class="fas fa-folder fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- إدارة الوسوم -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('tags.index') }}" class="text-decoration-none">
                    <div
                        class="card border-0 shadow-sm h-100 border-start border-secondary border-4 hover-shadow transition">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">الوسوم</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">إدارة الوسوم</div>
                            </div>
                            <div class="bg-secondary-subtle text-secondary p-2.5 rounded-circle">
                                <i class="fas fa-tags fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>

    </div>

    <style>
        .hover-shadow {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection
