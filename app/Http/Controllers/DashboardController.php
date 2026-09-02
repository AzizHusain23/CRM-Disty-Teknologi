<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Institution;
use App\Models\Registration;
use App\Models\Training;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $customerCounts = Customer::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $customerTotal = $customerCounts->sum();
        $prospectTotal = (int) ($customerCounts['prospect'] ?? 0);
        $activeTotal = (int) ($customerCounts['active'] ?? 0);
        $inactiveTotal = (int) ($customerCounts['inactive'] ?? 0);
        $repeatTotal = (int) ($customerCounts['repeat'] ?? 0);

        $institutionTotal = Institution::count();
        $trainingTotal = Training::count();
        $registrationTotal = Registration::count();

        $todayFollowUpTotal = FollowUp::where('status', 'pending')
            ->whereDate('follow_up_at', $today)
            ->count();

        $overdueFollowUpTotal = FollowUp::where('status', 'pending')
            ->where('follow_up_at', '<', now())
            ->count();

        $todayActivityTotal = Activity::whereDate('activity_at', $today)->count();

        $recentActivities = Activity::with(['customer', 'user'])
            ->latest('activity_at')
            ->limit(8)
            ->get();

        $upcomingFollowUps = FollowUp::with(['customer', 'assignedUser'])
            ->where('status', 'pending')
            ->where('follow_up_at', '>=', now())
            ->orderBy('follow_up_at')
            ->limit(8)
            ->get();

        $activityTypeCounts = Activity::query()
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'type');

        $registrationsThisMonth = Registration::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();

        $newCustomersThisMonth = Customer::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();

        $conversionBase = $prospectTotal + $activeTotal + $repeatTotal;
        $conversionTotal = $activeTotal + $repeatTotal;
        $conversionRate = $conversionBase > 0
            ? round(($conversionTotal / $conversionBase) * 100, 1)
            : 0;

        $sixMonthActivity = collect(range(5, 0))->map(function (int $monthsAgo) {
            $start = now()->subMonths($monthsAgo)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            return [
                'label' => $start->translatedFormat('M Y'),
                'total' => Activity::whereBetween('activity_at', [$start, $end])->count(),
            ];
        });

        $maxMonthlyActivity = max(1, $sixMonthActivity->max('total'));

        return view('dashboard.index', compact(
            'customerTotal',
            'prospectTotal',
            'activeTotal',
            'inactiveTotal',
            'repeatTotal',
            'institutionTotal',
            'trainingTotal',
            'registrationTotal',
            'todayFollowUpTotal',
            'overdueFollowUpTotal',
            'todayActivityTotal',
            'recentActivities',
            'upcomingFollowUps',
            'activityTypeCounts',
            'registrationsThisMonth',
            'newCustomersThisMonth',
            'conversionRate',
            'sixMonthActivity',
            'maxMonthlyActivity'
        ));
    }
}
