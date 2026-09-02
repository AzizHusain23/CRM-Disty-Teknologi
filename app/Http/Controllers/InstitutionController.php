<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['name', 'type', 'city', 'province', 'customers_count'], true)
            ? $sort
            : 'name';

        $direction = strtolower($request->string('direction')->toString());
        $direction = in_array($direction, ['asc', 'desc'], true)
            ? $direction
            : 'asc';

        $perPageOptions = [25, 50, 100, 200, 500];
        $perPage = (int) $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $institutions = Institution::query()
            ->withCount('customers')
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view(
            'institutions.index',
            compact('institutions', 'sort', 'direction', 'perPage', 'perPageOptions')
        );
    }

    public function create(): View
    {
        return view('institutions.create');
    }

    public function store(
        StoreInstitutionRequest $request
    ): RedirectResponse {
        $institution = Institution::create(
            $request->validated()
        );

        return redirect()
            ->route('institutions.index')
            ->with(
                'success',
                'Instansi berhasil ditambahkan.'
            );
    }

    public function show(
        Institution $institution
    ): View {
        $institution->load([
            'customers' => function ($query) {
                $query->latest();
            },
        ]);

        return view(
            'institutions.show',
            compact('institution')
        );
    }

    public function edit(
        Institution $institution
    ): View {
        return view(
            'institutions.edit',
            compact('institution')
        );
    }

    public function update(
        UpdateInstitutionRequest $request,
        Institution $institution
    ): RedirectResponse {
        $institution->update(
            $request->validated()
        );

        return redirect()
            ->route(
                'institutions.show',
                $institution
            )
            ->with(
                'success',
                'Data instansi berhasil diperbarui.'
            );
    }

    public function destroy(
        Institution $institution
    ): RedirectResponse {
        if ($institution->customers()->exists()) {
            return redirect()
                ->route('institutions.index')
                ->with(
                    'error',
                    'Instansi tidak dapat dihapus karena masih memiliki customer.'
                );
        }

        $institution->delete();

        return redirect()
            ->route('institutions.index')
            ->with(
                'success',
                'Instansi berhasil dihapus.'
            );
    }
}