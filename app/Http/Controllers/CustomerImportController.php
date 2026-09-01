<?php

namespace App\Http\Controllers;

use App\Exports\CustomerImportTemplateExport;
use App\Imports\CustomerExcelImport;
use App\Models\ImportBatch;
use App\Services\CustomerImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class CustomerImportController extends Controller
{
    private const REQUIRED_HEADERS = [
        'Nama',
        'Email',
        'Nomor Telepon',
        'Nomor Dokumen',
        'Nama Instansi',
        'Jenis Instansi',
        'Kota',
        'Provinsi',
    ];

    public function index(): View
    {
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
            ->latest()
            ->paginate(15);

        return view(
            'customer-imports.index',
            compact('batches')
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
        Request $request,
        CustomerImportService $service
    ): RedirectResponse {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:20480',
            ],
        ]);

        $file = $validated['file'];

        $storedPath = Storage::disk('local')
            ->putFile(
                'imports',
                $file
            );

        if ($storedPath === false) {
            return back()
                ->withErrors([
                    'file' =>
                        'File gagal disimpan ke server.',
                ])
                ->withInput();
        }

        $batch = ImportBatch::create([
            'user_id' =>
                $request->user()->id,

            'original_filename' =>
                $file->getClientOriginalName(),

            'stored_path' =>
                $storedPath,

            'status' =>
                'validating',
        ]);

        try {
            $absolutePath =
                Storage::disk('local')
                    ->path($storedPath);

            $this->validateWorkbook(
                $absolutePath
            );

            Excel::import(
                new CustomerExcelImport(
                    $batch,
                    $service
                ),
                $storedPath,
                'local'
            );

            $batch->refresh();

            $batch->update([
                'status' =>
                    'ready',

                'error_message' =>
                    null,
            ]);

            return redirect()
                ->route(
                    'customer-imports.show',
                    $batch
                )
                ->with(
                    'success',
                    'File berhasil dianalisis. Periksa hasil preview sebelum melakukan import.'
                );
        } catch (Throwable $e) {
            report($e);

            $batch->update([
                'status' =>
                    'failed',

                'error_message' =>
                    $e->getMessage(),
            ]);

            return redirect()
                ->route(
                    'customer-imports.show',
                    $batch
                )
                ->with(
                    'error',
                    'File tidak memenuhi standar import CRM.'
                );
        }
    }

    public function show(
        ImportBatch $importBatch
    ): View {
        $importBatch->load('user');

        $rows =
            $importBatch->rows()
                ->orderBy('sheet_name')
                ->orderBy('row_number')
                ->paginate(50)
                ->withQueryString();

        return view(
            'customer-imports.show',
            compact(
                'importBatch',
                'rows'
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

    private function validateWorkbook(
        string $path
    ): void {
        $spreadsheet =
            IOFactory::load($path);

        $sheetNames =
            $spreadsheet->getSheetNames();

        if (
            count($sheetNames) !== 1
        ) {
            throw new \RuntimeException(
                'File Excel harus memiliki tepat satu sheet dengan nama "customers".'
            );
        }

        if (
            $sheetNames[0] !== 'customers'
        ) {
            throw new \RuntimeException(
                'Nama sheet harus "customers".'
            );
        }

        $sheet =
            $spreadsheet
                ->getSheetByName(
                    'customers'
                );

        if ($sheet === null) {
            throw new \RuntimeException(
                'Sheet customers tidak ditemukan.'
            );
        }

        $headers =
            $sheet
                ->rangeToArray(
                    'A1:H1',
                    null,
                    true,
                    false
                )[0];

        $headers =
            array_map(
                static function ($value) {
                    return trim(
                        (string) $value
                    );
                },
                $headers
            );

        if (
            $headers
            !== self::REQUIRED_HEADERS
        ) {
            throw new \RuntimeException(
                'Header Excel tidak sesuai standar CRM. Gunakan tombol "Download Template Excel".'
            );
        }
    }
}
