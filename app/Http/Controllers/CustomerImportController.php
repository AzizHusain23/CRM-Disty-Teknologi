<?php

namespace App\Http\Controllers;

use App\Exports\CustomerImportTemplateExport;
use App\Imports\CustomerExcelImport;
use App\Models\ImportBatch;
use App\Services\CustomerImportService;
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
        $validated =
            $request->validate([
                'file' => [
                    'required',
                    'file',
                    'mimes:xlsx,xls',
                    'max:20480',
                ],
            ]);

        $file =
            $validated['file'];

        $storedPath =
            Storage::disk('local')
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

        $batch =
            ImportBatch::create([
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

            $batch->update([
                'status' =>
                    'ready',
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

    public function execute(
        Request $request,
        ImportBatch $importBatch,
        CustomerImportService $service
    ): RedirectResponse {
        if (
            $importBatch->status
            !== 'ready'
        ) {
            return back()
                ->with(
                    'error',
                    'Batch import ini belum siap untuk dijalankan.'
                );
        }

        if (
            !$importBatch
                ->rows()
                ->where(
                    'status',
                    'ready'
                )
                ->exists()
        ) {
            return back()
                ->with(
                    'error',
                    'Tidak ada data valid yang bisa diimport.'
                );
        }

        try {
            $service->execute(
                $importBatch
            );

            return redirect()
                ->route(
                    'customer-imports.show',
                    $importBatch
                )
                ->with(
                    'success',
                    'Data customer berhasil diimport ke CRM.'
                );
        } catch (Throwable $e) {
            report($e);

            $importBatch->update([
                'status' =>
                    'failed',

                'error_message' =>
                    $e->getMessage(),
            ]);

            return back()
                ->with(
                    'error',
                    'Import gagal dilakukan.'
                );
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