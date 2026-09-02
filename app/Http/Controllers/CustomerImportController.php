<?php

namespace App\Http\Controllers;

use App\Exports\CustomerImportTemplateExport;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Services\CustomerImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class CustomerImportController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['original_filename', 'imported_rows', 'total_rows', 'duplicate_rows', 'invalid_rows', 'status'], true)
            ? $sort
            : 'created_at';

        $direction = strtolower($request->string('direction')->toString());
        $direction = in_array($direction, ['asc', 'desc'], true)
            ? $direction
            : 'desc';

        $perPageOptions = [25, 50, 100, 200];
        $perPage = (int) $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $batches = ImportBatch::query()
            ->with('user')
            ->withCount([
                'rows as imported_rows' => function ($query) {
                    $query->where('status', 'imported');
                },
                'rows as remaining_rows' => function ($query) {
                    $query->where('status', 'ready');
                },
            ])
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view(
            'customer-imports.index',
            compact('batches', 'sort', 'direction', 'perPage', 'perPageOptions')
        );
    }

    public function create(): View
    {
        return view(
            'customer-imports.create'
        );
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new CustomerImportTemplateExport(),
            'CRM_Customer_Import_Template.xlsx'
        );
    }

    public function store(
        Request $request
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:20480',
            ],
        ]);

        $file = $validated['file'];

        $storedPath = Storage::disk('local')->putFile('imports', $file);

        if ($storedPath === false) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File gagal disimpan ke server.',
                ], 500);
            }

            return back()
                ->withErrors([
                    'file' => 'File gagal disimpan ke server.',
                ])
                ->withInput();
        }

        $batch = ImportBatch::create([
            'user_id' => $request->user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'status' => 'validating',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'batch_id' => $batch->id,
                'message' => 'File berhasil diupload. Validasi akan dijalankan secara bertahap.',
                'validate_url' => route('customer-imports.validate', $batch),
                'redirect_url' => route('customer-imports.show', $batch),
            ]);
        }

        return redirect()
            ->route('customer-imports.show', $batch)
            ->with(
                'success',
                'File berhasil diupload. Validasi akan dijalankan secara bertahap.'
            );
    }

    public function validateChunk(
        Request $request,
        ImportBatch $importBatch,
        CustomerImportService $service
    ): JsonResponse {
        if (in_array($importBatch->status, ['ready', 'processing', 'completed'], true)) {
            return response()->json([
                'success' => true,
                ...$service->getValidationProgress($importBatch),
            ]);
        }

        if ($importBatch->status === 'failed') {
            return response()->json([
                'success' => false,
                'message' => $importBatch->error_message ?: 'Validasi sebelumnya gagal.',
                'error_message' => $importBatch->error_message,
                ...$service->getValidationProgress($importBatch),
            ], 422);
        }

        try {
            $progress = $service->validateChunk($importBatch, 100);

            return response()->json($progress);
        } catch (Throwable $e) {
            report($e);

            $importBatch->refresh();
            $importBatch->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi file gagal dilakukan.',
                'error_message' => $e->getMessage(),
                ...$service->getValidationProgress($importBatch),
            ], 500);
        }
    }

    public function updateRow(
        Request $request,
        ImportBatch $importBatch,
        ImportRow $importRow,
        CustomerImportService $service
    ): JsonResponse {
        if ($importRow->import_batch_id !== $importBatch->id) {
            return response()->json([
                'success' => false,
                'message' => 'Baris import tidak sesuai dengan batch yang dipilih.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'institution_type' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $result = $service->updateValidationRow($importBatch, $importRow, $validated);

            return response()->json($result);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Baris gagal diperbarui.',
                'error_message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(
        Request $request,
        ImportBatch $importBatch
    ): View {
        $importBatch->load('user');

        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, [
            'sheet_name',
            'row_number',
            'name',
            'email',
            'phone',
            'institution_name',
            'institution_type',
            'status',
        ], true)
            ? $sort
            : 'row_number';

        $direction = strtolower($request->string('direction')->toString());
        $direction = in_array($direction, ['asc', 'desc'], true)
            ? $direction
            : 'asc';

        $perPageOptions = [25, 50, 100, 200, 500];
        $perPage = (int) $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $rowsQuery = $importBatch->rows();

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if (in_array($status, ['ready', 'duplicate', 'invalid', 'imported'], true)) {
                $rowsQuery->where('status', $status);
            }
        }

        $rows = $rowsQuery
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view(
            'customer-imports.show',
            compact(
                'importBatch',
                'rows',
                'sort',
                'direction',
                'perPage',
                'perPageOptions'
            )
        );
    }

    /**
     * Proses satu chunk import saja.
     *
     * Endpoint ini sengaja tidak memproses seluruh batch.
     * Frontend akan memanggil endpoint ini berulang kali.
     */
    public function execute(
        Request $request,
        ImportBatch $importBatch,
        CustomerImportService $service
    ): JsonResponse|RedirectResponse {
        if (
            !in_array(
                $importBatch->status,
                [
                    'ready',
                    'processing',
                    'failed',
                ],
                true
            )
        ) {
            return $this->executeResponse(
                $request,
                $importBatch,
                false,
                'Batch import ini belum siap untuk dijalankan.'
            );
        }

        $hasReadyRows =
            $importBatch
                ->rows()
                ->where(
                    'status',
                    'ready'
                )
                ->exists();

        if (!$hasReadyRows) {
            $importBatch->update([
                'status' =>
                    'completed',

                'completed_at' =>
                    $importBatch->completed_at
                        ?? now(),

                'error_message' =>
                    null,
            ]);

            $progress =
                $service->getProgress(
                    $importBatch
                );

            return $this->executeResponse(
                $request,
                $importBatch,
                true,
                'Import sudah selesai.',
                $progress
            );
        }

        try {
            $progress =
                $service->executeChunk(
                    $importBatch,
                    25
                );

            return $this->executeResponse(
                $request,
                $importBatch,
                true,
                $progress['status'] === 'completed'
                    ? 'Import selesai.'
                    : 'Chunk import berhasil diproses.',
                $progress
            );
        } catch (Throwable $e) {
            report($e);

            $importBatch->refresh();

            $importBatch->update([
                'status' =>
                    'failed',

                'error_message' =>
                    $e->getMessage(),
            ]);

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Terjadi kesalahan saat memproses import.',

                'error' =>
                    $e->getMessage(),

                ...$service->getProgress(
                    $importBatch
                ),
            ], 500);
        }
    }

    public function destroy(
        ImportBatch $importBatch
    ): RedirectResponse {
        if (
            $importBatch->status
            === 'completed'
        ) {
            return back()
                ->with(
                    'error',
                    'Batch import yang sudah selesai tidak dapat dihapus dari riwayat.'
                );
        }

        if (
            $importBatch->status
            === 'processing'
        ) {
            return back()
                ->with(
                    'error',
                    'Batch import yang sedang diproses tidak dapat dihapus.'
                );
        }

        if (
            Storage::disk('local')
                ->exists(
                    $importBatch->stored_path
                )
        ) {
            Storage::disk('local')
                ->delete(
                    $importBatch->stored_path
                );
        }

        $importBatch->delete();

        return redirect()
            ->route(
                'customer-imports.index'
            )
            ->with(
                'success',
                'Batch import berhasil dihapus.'
            );
    }

    private function executeResponse(
        Request $request,
        ImportBatch $importBatch,
        bool $success,
        string $message,
        ?array $progress = null
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'success' =>
                    $success,

                'message' =>
                    $message,

                ...($progress ?? []),
            ]);
        }

        if ($success) {
            return redirect()
                ->route(
                    'customer-imports.show',
                    $importBatch
                )
                ->with(
                    'success',
                    $message
                );
        }

        return redirect()
            ->route(
                'customer-imports.show',
                $importBatch
            )
            ->with(
                'error',
                $message
            );
    }

}
