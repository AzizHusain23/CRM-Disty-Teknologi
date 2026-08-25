<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()
            ->with('company');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString()
            );
        }

        if ($request->filled('company_id')) {
            $query->where(
                'company_id',
                $request->integer('company_id')
            );
        }

        $customers = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $companies = Company::query()
            ->orderBy('name')
            ->get();

        return view(
            'customers.index',
            compact('customers', 'companies')
        );
    }

    public function create(): View
    {
        $companies = Company::query()
            ->orderBy('name')
            ->get();

        return view('customers.create', compact('companies'));
    }

    public function store(
        StoreCustomerRequest $request
    ): RedirectResponse {
        $customer = DB::transaction(function () use ($request) {
            $customer = Customer::create([
                'customer_code' => 'TEMP-' . uniqid(),
                ...$request->validated(),
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
            ->with('success', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'company',
            'registrations.training.category',
            'activities.user',
            'followUps.assignedUser',
        ]);

        return view(
            'customers.show',
            compact('customer')
        );
    }

    public function edit(Customer $customer): View
    {
        $companies = Company::query()
            ->orderBy('name')
            ->get();

        return view(
            'customers.edit',
            compact('customer', 'companies')
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
            ->route('customers.show', $customer)
            ->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }
}