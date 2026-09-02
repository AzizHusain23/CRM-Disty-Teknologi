<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = Company::query()
            ->withCount('customers')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        Company::create($request->validated());

        return redirect()
            ->route('companies.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function show(Company $company): View
    {
        $company->load([
            'customers' => function ($query) {
                $query->latest();
            },
        ]);

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company): View
    {
        return view('companies.edit', compact('company'));
    }

    public function update(
        UpdateCompanyRequest $request,
        Company $company
    ): RedirectResponse {
        $company->update($request->validated());

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Data perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        if ($company->customers()->exists()) {
            return redirect()
                ->route('companies.index')
                ->with('error', 'Perusahaan tidak dapat dihapus karena masih memiliki customer.');
        }

        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}