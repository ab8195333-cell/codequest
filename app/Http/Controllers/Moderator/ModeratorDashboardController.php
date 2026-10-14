<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Report;
use App\Models\User;

class ModeratorDashboardController extends Controller
{
    public function index()
    {
        // إحصائيات سريعة خاصة بمهام الإشراف اليومية
        $pendingReportsCount = Report::where('status', 'pending')->count();
        $recentUsersCount = User::where('created_at', '>=', now()->subDays(7))->count();
        $totalQuestionsCount = Question::count();

        // أحدث البلاغات التي تتطلب اتخاذ إجراء
        $latestReports = Report::with(['user', 'reportable'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.Moderator.index', compact(
            'pendingReportsCount',
            'recentUsersCount',
            'totalQuestionsCount',
            'latestReports'
        ));
    }
}
