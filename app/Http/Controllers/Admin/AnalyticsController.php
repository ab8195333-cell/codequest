<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. بطاقات الإحصائيات السريعة (KPI Cards)
        $totalUsers = User::count();
        $totalQuestions = Question::count();
        $totalAnswers = Answer::count();
        $totalReports = Report::count();

        // 2. تجميع تسجيلات المستخدمين لآخر 6 أشهر للرسم البياني
        $monthlyUsers = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // أسماء الشهور للعرض في المخطط
        $monthsLabels = [];
        $usersData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthsLabels[] = $date->translatedFormat('F'); // اسم الشهر بالعربي
            $usersData[] = $monthlyUsers->get($date->month, 0);
        }

        // 3. توزيع البلاغات حسب الحالة (رسم دائرى Doughnut)
        $reportsStatus = Report::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard.admin.index', compact(
            'totalUsers',
            'totalQuestions',
            'totalAnswers',
            'totalReports',
            'monthsLabels',
            'usersData',
            'reportsStatus'
        ));
    }
}
