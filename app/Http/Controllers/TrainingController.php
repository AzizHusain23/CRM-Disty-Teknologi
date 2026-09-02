<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TrainingController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['name', 'category', 'price', 'duration_hours', 'registrations_count', 'is_active'], true)
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

        $query = Training::query()
            ->with('category')
            ->withCount('registrations');

        if ($sort === 'category') {
            $query->orderBy(
                TrainingCategory::query()
                    ->select('name')
                    ->whereColumn('training_categories.id', 'trainings.training_category_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $trainings = $query
            ->paginate($perPage);

        return view(
            'trainings.index',
            compact('trainings', 'sort', 'direction', 'perPage', 'perPageOptions')
        );
    }

    public function create(): View
    {
        $categories =
            TrainingCategory::query()
                ->where(
                    'is_active',
                    true
                )
                ->orderBy('name')
                ->get();

        return view(
            'trainings.create',
            compact('categories')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'training_category_id' => [
                'nullable',
                'exists:training_categories,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'duration_hours' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' =>
                'Nama pelatihan wajib diisi.',

            'training_category_id.exists' =>
                'Kategori pelatihan tidak valid.',

            'price.numeric' =>
                'Harga harus berupa angka.',

            'price.min' =>
                'Harga tidak boleh negatif.',

            'duration_hours.integer' =>
                'Durasi harus berupa angka bulat.',

            'duration_hours.min' =>
                'Durasi minimal 1 jam.',
        ]);

        Training::create([
            'training_category_id' =>
                $validated[
                    'training_category_id'
                ] ?? null,

            'name' =>
                trim($validated['name']),

            'description' =>
                $validated['description']
                    ?? null,

            'price' =>
                $validated['price']
                    ?? null,

            'duration_hours' =>
                $validated['duration_hours']
                    ?? null,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);

        return redirect()
            ->route(
                'trainings.index'
            )
            ->with(
                'success',
                'Pelatihan berhasil ditambahkan.'
            );
    }

    public function show(
        Training $training
    ): View {
        $training->load([
            'category',
            'registrations.customer.institution',
            'schedules.trainer',
            'schedules.registrations',
        ]);

        return view(
            'trainings.show',
            compact('training')
        );
    }

    public function edit(
        Training $training
    ): View {
        $categories =
            TrainingCategory::query()
                ->orderBy('name')
                ->get();

        return view(
            'trainings.edit',
            compact(
                'training',
                'categories'
            )
        );
    }

    public function update(
        Request $request,
        Training $training
    ): RedirectResponse {
        $validated = $request->validate([
            'training_category_id' => [
                'nullable',
                'exists:training_categories,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'duration_hours' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' =>
                'Nama pelatihan wajib diisi.',

            'training_category_id.exists' =>
                'Kategori pelatihan tidak valid.',

            'price.numeric' =>
                'Harga harus berupa angka.',

            'price.min' =>
                'Harga tidak boleh negatif.',

            'duration_hours.integer' =>
                'Durasi harus berupa angka bulat.',

            'duration_hours.min' =>
                'Durasi minimal 1 jam.',
        ]);

        $training->update([
            'training_category_id' =>
                $validated[
                    'training_category_id'
                ] ?? null,

            'name' =>
                trim($validated['name']),

            'description' =>
                $validated['description']
                    ?? null,

            'price' =>
                $validated['price']
                    ?? null,

            'duration_hours' =>
                $validated['duration_hours']
                    ?? null,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);

        return redirect()
            ->route(
                'trainings.show',
                $training
            )
            ->with(
                'success',
                'Pelatihan berhasil diperbarui.'
            );
    }

    public function destroy(
        Training $training
    ): RedirectResponse {
        if (
            $training
                ->registrations()
                ->exists()
        ) {
            return back()
                ->with(
                    'error',
                    'Pelatihan tidak dapat dihapus karena sudah memiliki data peserta/registrasi.'
                );
        }

        try {
            $training->delete();

            return redirect()
                ->route(
                    'trainings.index'
                )
                ->with(
                    'success',
                    'Pelatihan berhasil dihapus.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->with(
                    'error',
                    'Pelatihan gagal dihapus.'
                );
        }
    }
}
