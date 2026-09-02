<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class FollowUpController extends Controller
{
    private array $sortColumns = [
        'follow_up_at',
        'title',
        'priority',
        'status',
        'customer',
        'assigned_to',
        'created_at',
    ];

    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, $this->sortColumns, true) ? $sort : 'follow_up_at';

        $direction = strtolower($request->string('direction')->toString());
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc';

        $perPageOptions = [25, 50, 100, 200, 500];
        $perPage = (int) $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $query = FollowUp::query()
            ->with(['customer', 'assignedUser']);

        $search = trim($request->string('search')->toString());
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('customer_code', 'like', "%{$search}%");
                    });
            });
        }

        $statuses = ['pending', 'completed', 'cancelled'];
        if ($request->filled('status') && in_array($request->string('status')->toString(), $statuses, true)) {
            $query->where('status', $request->string('status')->toString());
        }

        $priorities = ['low', 'normal', 'high'];
        if ($request->filled('priority') && in_array($request->string('priority')->toString(), $priorities, true)) {
            $query->where('priority', $request->string('priority')->toString());
        }

        if ($request->filled('assigned_to') && $request->integer('assigned_to') > 0) {
            $query->where('assigned_to', $request->integer('assigned_to'));
        }

        if ($sort === 'customer') {
            $query->orderBy(
                Customer::query()
                    ->select('name')
                    ->whereColumn('customers.id', 'follow_ups.customer_id'),
                $direction
            );
        } elseif ($sort === 'assigned_to') {
            $query->orderBy(
                User::query()
                    ->select('name')
                    ->whereColumn('users.id', 'follow_ups.assigned_to'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $followUps = $query
            ->paginate($perPage)
            ->withQueryString();

        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('follow-ups.index', compact(
            'followUps',
            'sort',
            'direction',
            'perPage',
            'perPageOptions',
            'users'
        ));
    }

    public function create(Request $request): View
    {
        $customers = Customer::query()
            ->orderBy('name')
            ->get(['id', 'name', 'customer_code', 'status']);

        $users = User::query()->orderBy('name')->get(['id', 'name']);
        $selectedCustomerId = $request->integer('customer_id') ?: null;

        return view('follow-ups.create', compact('customers', 'users', 'selectedCustomerId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        FollowUp::create([
            'customer_id' => $validated['customer_id'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title' => trim($validated['title']),
            'description' => $validated['description'] ?? null,
            'follow_up_at' => $validated['follow_up_at'],
            'priority' => $validated['priority'],
            'status' => 'pending',
            'completed_at' => null,
        ]);

        return redirect()
            ->route('follow-ups.index')
            ->with('success', 'Follow up berhasil dibuat.');
    }

    public function edit(FollowUp $followUp): View
    {
        $customers = Customer::query()
            ->orderBy('name')
            ->get(['id', 'name', 'customer_code', 'status']);

        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('follow-ups.edit', compact('followUp', 'customers', 'users'));
    }

    public function update(Request $request, FollowUp $followUp): RedirectResponse
    {
        $validated = $this->validateData($request);

        $followUp->update([
            'customer_id' => $validated['customer_id'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title' => trim($validated['title']),
            'description' => $validated['description'] ?? null,
            'follow_up_at' => $validated['follow_up_at'],
            'priority' => $validated['priority'],
        ]);

        return redirect()
            ->route('follow-ups.index')
            ->with('success', 'Follow up berhasil diperbarui.');
    }

    public function complete(FollowUp $followUp): RedirectResponse
    {
        if ($followUp->status !== 'pending') {
            return back()->with('error', 'Hanya follow up berstatus Pending yang dapat diselesaikan.');
        }

        DB::transaction(function () use ($followUp) {
            $followUp->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        return back()->with('success', 'Follow up ditandai sebagai selesai.');
    }

    public function cancel(FollowUp $followUp): RedirectResponse
    {
        if ($followUp->status !== 'pending') {
            return back()->with('error', 'Hanya follow up berstatus Pending yang dapat dibatalkan.');
        }

        $followUp->update([
            'status' => 'cancelled',
            'completed_at' => null,
        ]);

        return back()->with('success', 'Follow up dibatalkan.');
    }

    public function destroy(FollowUp $followUp): RedirectResponse
    {
        $followUp->delete();

        return back()->with('success', 'Follow up berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'follow_up_at' => ['required', 'date'],
            'priority' => ['required', 'in:low,normal,high'],
        ], [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak valid.',
            'assigned_to.exists' => 'PIC tidak valid.',
            'title.required' => 'Judul follow up wajib diisi.',
            'follow_up_at.required' => 'Tanggal follow up wajib diisi.',
            'follow_up_at.date' => 'Tanggal follow up tidak valid.',
            'priority.required' => 'Prioritas wajib dipilih.',
            'priority.in' => 'Prioritas tidak valid.',
        ]);
    }
}
