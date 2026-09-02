<?php

namespace App\Http\Controllers;

use App\Models\TrainingCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TrainingCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['name', 'description', 'trainings_count', 'is_active'], true)
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

        $categories = TrainingCategory::query()
            ->withCount('trainings')
            ->orderBy($sort, $direction)
            ->paginate($perPage);

        return view(
            'training-categories.index',
            compact('categories', 'sort', 'direction', 'perPage', 'perPageOptions')
        );
    }

    public function create(): View
    {
        return view(
            'training-categories.create'
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:training_categories,name',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' =>
                'Nama kategori wajib diisi.',

            'name.unique' =>
                'Nama kategori sudah digunakan.',
        ]);

        TrainingCategory::create([
            'name' =>
                trim($validated['name']),

            'description' =>
                $validated['description']
                    ?? null,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);

        return redirect()
            ->route(
                'training-categories.index'
            )
            ->with(
                'success',
                'Kategori pelatihan berhasil ditambahkan.'
            );
    }

    public function edit(
        TrainingCategory $trainingCategory
    ): View {
        return view(
            'training-categories.edit',
            compact('trainingCategory')
        );
    }

    public function update(
        Request $request,
        TrainingCategory $trainingCategory
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:training_categories,name,'
                    . $trainingCategory->id,
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' =>
                'Nama kategori wajib diisi.',

            'name.unique' =>
                'Nama kategori sudah digunakan.',
        ]);

        $trainingCategory->update([
            'name' =>
                trim($validated['name']),

            'description' =>
                $validated['description']
                    ?? null,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);

        return redirect()
            ->route(
                'training-categories.index'
            )
            ->with(
                'success',
                'Kategori pelatihan berhasil diperbarui.'
            );
    }

    public function destroy(
        TrainingCategory $trainingCategory
    ): RedirectResponse {
        if (
            $trainingCategory
                ->trainings()
                ->exists()
        ) {
            return back()
                ->with(
                    'error',
                    'Kategori tidak dapat dihapus karena masih digunakan oleh pelatihan.'
                );
        }

        try {
            $trainingCategory->delete();

            return redirect()
                ->route(
                    'training-categories.index'
                )
                ->with(
                    'success',
                    'Kategori pelatihan berhasil dihapus.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->with(
                    'error',
                    'Kategori pelatihan gagal dihapus.'
                );
        }
    }
}
