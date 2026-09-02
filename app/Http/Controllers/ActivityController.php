<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActivityController extends Controller
{
    private array $sortColumns = [
        'activity_at',
        'type',
        'subject',
        'customer',
        'user',
        'created_at',
    ];

    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, $this->sortColumns, true) ? $sort : 'activity_at';

        $direction = strtolower($request->string('direction')->toString());
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';

        $perPageOptions = [25, 50, 100, 200, 500];
        $perPage = (int) $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $query = Activity::query()->with(['customer', 'user']);

        $search = trim($request->string('search')->toString());
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('customer_code', 'like', "%{$search}%");
                    });
            });
        }

        $types = ['phone_call', 'whatsapp', 'meeting', 'visit', 'note'];
        if ($request->filled('type') && in_array($request->string('type')->toString(), $types, true)) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('user_id') && $request->integer('user_id') > 0) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($sort === 'customer') {
            $query->orderBy(
                Customer::query()
                    ->select('name')
                    ->whereColumn('customers.id', 'activities.customer_id'),
                $direction
            );
        } elseif ($sort === 'user') {
            $query->orderBy(
                User::query()
                    ->select('name')
                    ->whereColumn('users.id', 'activities.user_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $activities = $query
            ->paginate($perPage)
            ->withQueryString();

        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('activities.index', compact(
            'activities',
            'sort',
            'direction',
            'perPage',
            'perPageOptions',
            'users'
        ));
    }

    public function store(
        Request $request,
        Customer $customer
    ): RedirectResponse {
        $validated = $request->validate([
            'type' => [
                'required',
                'in:phone_call,whatsapp,meeting,visit,note',
            ],
            'subject' => [
                'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'activity_at' => [
                'required',
                'date',
            ],
            'activate_customer' => [
                'nullable',
                'boolean',
            ],
        ], [
            'type.required' => 'Jenis aktivitas wajib dipilih.',
            'type.in' => 'Jenis aktivitas tidak valid.',
            'activity_at.required' => 'Tanggal aktivitas wajib diisi.',
            'activity_at.date' => 'Tanggal aktivitas tidak valid.',
        ]);

        $shouldActivate = $request->boolean('activate_customer');

        if ($shouldActivate && $customer->status !== 'prospect') {
            return back()
                ->withInput()
                ->withErrors([
                    'activate_customer' => 'Hanya customer berstatus Prospect yang dapat dikonversi menjadi Active melalui aktivitas.',
                ]);
        }

        DB::transaction(function () use ($validated, $request, $customer, $shouldActivate) {
            Activity::create([
                'customer_id' => $customer->id,
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'subject' => $validated['subject'] ?? null,
                'description' => $validated['description'] ?? null,
                'activity_at' => $validated['activity_at'],
            ]);

            if ($shouldActivate) {
                $customer->update(['status' => 'active']);
            }
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with(
                'success',
                $shouldActivate
                    ? 'Aktivitas dicatat dan customer diubah menjadi Active.'
                    : 'Aktivitas customer berhasil dicatat.'
            );
    }
}
