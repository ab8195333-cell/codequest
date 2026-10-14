@extends('layouts.dashboard') {{-- أو القالب المعتمد للوحة التحكم --}}

@section('content')
    <div class="container-fluid py-4">
        <!-- عنوان الواجهة + الترحيب -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="font-weight-bold text-dark mb-1">لوحة إشراف المحتوى</h3>
                <p class="text-muted small mb-0">مرحباً بك {{ auth()->user()->name }}، يمكنك متابعة البلاغات والمحتوى من هنا.
                </p>
            </div>


        </div>

        <!-- كروت الإحصائيات السريعة للمشرف -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm border-left-danger py-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">البلاغات المعلقة</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $pendingReportsCount }}</div>
                        </div>
                        <i class="bi bi-exclamation-octagon fs-2 text-danger"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm border-left-primary py-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">المستخدمين الجدد (هذا
                                الأسبوع)</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $recentUsersCount }}</div>
                        </div>
                        <i class="bi bi-people fs-2 text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm border-left-success py-2">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">إجمالي الأسئلة</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalQuestionsCount }}</div>
                        </div>
                        <i class="bi bi-question-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول سريع لأحدث البلاغات المعلقة -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-bell mr-2 text-warning"></i> أحدث البلاغات التي تحتاج مراجعة</span>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">عرض كل
                    البلاغات</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>المُبَلِّغ</th>
                                <th>السبب</th>
                                <th>التاريخ</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestReports as $report)
                                <tr>
                                    <td>{{ $report->report_id }}</td>
                                    <td>{{ $report->user->name ?? 'غير محدد' }}</td>
                                    <td><span class="badge badge-light text-dark">{{ $report->reason }}</span></td>
                                    <td>{{ $report->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-primary">معالجة</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">لا توجد بلاغات معلقة حالياً 🎉
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
