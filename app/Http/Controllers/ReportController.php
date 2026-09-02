<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Institution;
use App\Models\Registration;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth();

        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $customerStatusCounts = Customer::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $activityTypeCounts = Activity::query()
            ->whereBetween('activity_at', [$start, $end])
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->orderByDesc('total')
            ->pluck('total', 'type');

        $followUpStatusCounts = FollowUp::query()
            ->whereBetween('follow_up_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $registrationStatusCounts = Registration::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topTrainings = Training::query()
            ->withCount(['registrations' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }])
            ->orderByDesc('registrations_count')
            ->limit(10)
            ->get();

        $topInstitutions = Institution::query()
            ->withCount(['customers' => function ($query) use ($start, $end) {
                $query->whereBetween('customers.created_at', [$start, $end]);
            }])
            ->orderByDesc('customers_count')
            ->limit(10)
            ->get();

        $newCustomers = Customer::whereBetween('created_at', [$start, $end])->count();
        $activities = Activity::whereBetween('activity_at', [$start, $end])->count();
        $followUps = FollowUp::whereBetween('follow_up_at', [$start, $end])->count();
        $registrations = Registration::whereBetween('created_at', [$start, $end])->count();
        $registrationRevenue = (float) (Registration::whereBetween('created_at', [$start, $end])->sum('amount'));

        $completedFollowUps = (int) ($followUpStatusCounts['completed'] ?? 0);
        $followUpCompletionRate = $followUps > 0
            ? round(($completedFollowUps / $followUps) * 100, 1)
            : 0;

        return view('reports.index', compact(
            'start',
            'end',
            'customerStatusCounts',
            'activityTypeCounts',
            'followUpStatusCounts',
            'registrationStatusCounts',
            'topTrainings',
            'topInstitutions',
            'newCustomers',
            'activities',
            'followUps',
            'registrations',
            'registrationRevenue',
            'followUpCompletionRate'
        ));
    }
}
