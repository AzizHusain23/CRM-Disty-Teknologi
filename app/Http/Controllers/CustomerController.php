<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    private const PER_PAGE_OPTIONS = [25, 50, 100, 200, 500];

    private const SORTABLE_COLUMNS = [
        'name',
        'customer_code',
        'email',
        'phone',
        'status',
        'institution',
    ];

    public function index(Request $request): View
    {
        $query = Customer::query()
            ->with('institution');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhereHas('institution', function ($institutionQuery) use ($search) {
                        $institutionQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString()
            );
        }

        if ($request->filled('institution_id')) {
            $query->where(
                'institution_id',
                $request->integer('institution_id')
            );
        }

        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, self::SORTABLE_COLUMNS, true)
            ? $sort
            : 'customer_code';

        $direction = strtolower(
            $request->string('direction')->toString()
        );

        $direction = in_array($direction, ['asc', 'desc'], true)
            ? $direction
            : 'desc';

        if ($sort === 'institution') {
            $query->orderBy(
                Institution::query()
                    ->select('name')
                    ->whereColumn('institutions.id', 'customers.institution_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $perPageOptions = self::PER_PAGE_OPTIONS;

        $perPage = (int) $request->integer('per_page', 50);

        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 50;
        }

        $customers = $query
            ->paginate($perPage)
            ->withQueryString();

        $institutions = Institution::query()
            ->orderBy('name')
            ->get();

        return view(
            'customers.index',
            compact(
                'customers',
                'institutions',
                'sort',
                'direction',
                'perPage',
                'perPageOptions'
            )
        );
    }

    public function create(): View
    {
        $institutions = Institution::query()
            ->orderBy('name')
            ->get();

        return view(
            'customers.create',
            compact('institutions')
        );
    }

    public function store(
        StoreCustomerRequest $request
    ): RedirectResponse {
        $customer = DB::transaction(function () use ($request) {
            $customer = Customer::create([
                'customer_code' => 'TEMP-' . uniqid(),
                'institution_id' => $request->validated('institution_id'),
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'phone' => $request->validated('phone'),
                'document_number' => $request->validated('document_number'),
                'city' => $request->validated('city'),
                'province' => $request->validated('province'),
                'status' => 'prospect',
                'source' => 'manual',
                'notes' => $request->validated('notes'),
            ]);

            $customer->update([
                'customer_code' => sprintf(
                    'CUS-%06d',
                    $customer->id
                ),
            ]);

            return $customer;
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with(
                'success',
                'Customer berhasil ditambahkan sebagai Prospect.'
            );
    }

    public function show(
        Customer $customer
    ): View {
        $customer->load([
            'institution',
            'registrations.training.category',
            'activities.user',
            'followUps.assignedUser',
        ]);

        return view(
            'customers.show',
            compact('customer')
        );
    }


    public function activate(Customer $customer): RedirectResponse
    {
        if ($customer->status === 'active') {
            return redirect()
                ->route('customers.show', $customer)
                ->with('error', 'Customer sudah berstatus Active.');
        }

        $customer->update([
            'status' => 'active',
        ]);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer berhasil dikonversi menjadi Active.');
    }

    public function deactivate(Customer $customer): RedirectResponse
    {
        if ($customer->status !== 'active') {
            return redirect()
                ->route('customers.show', $customer)
                ->with('error', 'Hanya customer Active yang dapat diubah menjadi Inactive.');
        }

        $customer->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer berhasil diubah menjadi Inactive.');
    }

    public function edit(
        Customer $customer
    ): View {
        $institutions = Institution::query()
            ->orderBy('name')
            ->get();

        return view(
            'customers.edit',
            compact(
                'customer',
                'institutions'
            )
        );
    }

    public function update(
        UpdateCustomerRequest $request,
        Customer $customer
    ): RedirectResponse {
        $customer->update(
            $request->validated()
        );

        return redirect()
            ->route(
                'customers.show',
                $customer
            )
            ->with(
                'success',
                'Data customer berhasil diperbarui.'
            );
    }

    public function destroy(
        Customer $customer
    ): RedirectResponse {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with(
                'success',
                'Customer berhasil dihapus.'
            );
    }
}
