@extends('layouts.dashboard') {{-- أو قالب لوحة التحكم الخاط بك --}}

@section('content')
    <div class="container-fluid py-4">
        <h3 class="mb-4 font-weight-bold text-dark">لوحة الإحصائيات التحليلية</h3>

        <!-- بطاقات المؤشرات الرئيسية (KPIs) -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-left-primary py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">المستخدمين</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-left-success py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">الأسئلة</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalQuestions) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-left-info py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">الأجوبة</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAnswers) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-left-danger py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">البلاغات</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalReports) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم الرسوم البيانية -->
        <div class="row">
            <!-- رسم بياني خطي لمعدل انضمام المستخدمين -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold">معدل نمو المستخدمين (آخر 6 أشهر)</div>
                    <div class="card-body">
                        <canvas id="usersGrowthChart" style="max-height: 320px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- رسم دائرى لحالات البلاغات -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold">توزيع البلاغات حسب الحالة</div>
                    <div class="card-body">
                        <canvas id="reportsStatusChart" style="max-height: 320px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تضمين مكتبة Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Chart.js: نمو المستخدمين (Line Chart)
            const ctxUsers = document.getElementById('usersGrowthChart').getContext('2d');
            new Chart(ctxUsers, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthsLabels) !!},
                    datasets: [{
                        label: 'مستخدمين جُدد',
                        data: {!! json_encode($usersData) !!},
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // 2. Chart.js: حالات البلاغات (Doughnut Chart)
            const ctxReports = document.getElementById('reportsStatusChart').getContext('2d');
            new Chart(ctxReports, {
                type: 'doughnut',
                data: {
                    labels: ['قيد الانتظار', 'محلول'],
                    datasets: [{
                        data: [
                            {{ $reportsStatus['pending'] ?? 0 }},
                            {{ $reportsStatus['resolved'] ?? 0 }}
                        ],
                        backgroundColor: ['#f6c23e', '#1cc88a']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection
