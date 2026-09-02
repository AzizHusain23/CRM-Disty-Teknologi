<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Models\Institution;
use App\Imports\CustomerValidationReadFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerImportService
{
    private const ALLOWED_INSTITUTION_TYPES = [
        'Pemerintah' => 'government',
        'Sekolah' => 'school',
        'Perguruan Tinggi' => 'university',
        'Perusahaan' => 'company',
        'Yayasan' => 'foundation',
        'Lembaga' => 'institution',
        'Lainnya' => 'other',
    ];

    /**
     * Validasi file Excel secara bertahap.
     *
     * Satu request hanya membaca sebagian baris agar proses tidak menahan
     * satu HTTP request selama puluhan detik.
     */
    public function validateChunk(
        ImportBatch $batch,
        int $chunkSize = 100
    ): array {
        $chunkSize = max(25, min($chunkSize, 250));

        if ((int) $batch->total_rows === 0 && $batch->rows()->count() === 0) {
            $this->prepareValidation($batch);
        }

        $batch->refresh();
        $total = (int) $batch->total_rows;

        if ($total === 0) {
            $batch->update([
                'status' => 'ready',
                'error_message' => null,
            ]);

            return $this->getValidationProgress($batch);
        }

        $processed = $batch->rows()->count();

        if ($processed >= $total) {
            $batch->update([
                'status' => 'ready',
                'error_message' => null,
            ]);

            return $this->getValidationProgress($batch);
        }

        $startRow = $processed + 2;
        $endRow = min($startRow + $chunkSize - 1, $total + 1);

        $path = \Storage::disk('local')->path($batch->stored_path);

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(
            new CustomerValidationReadFilter($startRow, $endRow)
        );

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName('customers');

        if ($sheet === null) {
            throw new \RuntimeException('Sheet customers tidak ditemukan.');
        }

        $values = $sheet->rangeToArray(
            "A{$startRow}:H{$endRow}",
            null,
            true,
            false
        );

        $keys = [
            'nama',
            'email',
            'nomor_telepon',
            'nomor_dokumen',
            'nama_instansi',
            'jenis_instansi',
            'kota',
            'provinsi',
        ];

        $existingKeys = $this->getExistingCustomerKeys();
        $batchKeys = $batch->rows()
            ->where('status', 'ready')
            ->whereNotNull('dedupe_key')
            ->pluck('dedupe_key')
            ->flip()
            ->all();

        $readyCount = 0;
        $duplicateCount = 0;
        $invalidCount = 0;

        DB::transaction(function () use (
            $batch,
            $values,
            $keys,
            $startRow,
            $existingKeys,
            &$batchKeys,
            &$readyCount,
            &$duplicateCount,
            &$invalidCount,
        ): void {
            foreach ($values as $offset => $valuesRow) {
                $rawData = [];

                foreach ($keys as $index => $key) {
                    $rawData[$key] = $valuesRow[$index] ?? null;
                }

                $data = $this->normalizeRow($rawData);
                $analysis = $this->analyzeRow($data, $existingKeys, $batchKeys);

                ImportRow::create([
                    'import_batch_id' => $batch->id,
                    'sheet_name' => 'customers',
                    'row_number' => ($startRow + $offset),
                    'raw_data' => $rawData,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'document_number' => $data['document_number'],
                    'institution_name' => $data['institution_name'],
                    'institution_type' => $data['institution_type'],
                    'normalized_name' => $data['normalized_name'],
                    'normalized_email' => $data['normalized_email'],
                    'normalized_document_number' => $data['normalized_document_number'],
                    'dedupe_key' => $analysis['dedupe_key'],
                    'status' => $analysis['status'],
                    'duplicate_reason' => $analysis['duplicate_reason'],
                    'error_message' => $analysis['error_message'],
                ]);

                if ($analysis['status'] === 'ready') {
                    $readyCount++;
                    if ($analysis['dedupe_key'] !== null) {
                        $batchKeys[$analysis['dedupe_key']] = true;
                    }
                } elseif ($analysis['status'] === 'duplicate') {
                    $duplicateCount++;
                } else {
                    $invalidCount++;
                }
            }
        });

        if ($readyCount > 0) {
            $batch->increment('ready_rows', $readyCount);
        }

        if ($duplicateCount > 0) {
            $batch->increment('duplicate_rows', $duplicateCount);
        }

        if ($invalidCount > 0) {
            $batch->increment('invalid_rows', $invalidCount);
        }

        $batch->refresh();
        $validated = $batch->rows()->count();

        $batch->update([
            'status' => $validated >= $total ? 'ready' : 'validating',
            'error_message' => null,
        ]);

        return $this->getValidationProgress($batch);
    }

    public function getValidationProgress(ImportBatch $batch): array
    {
        $batch->refresh();

        $validated = $batch->rows()->count();
        $total = (int) $batch->total_rows;
        $ready = (int) $batch->ready_rows;
        $duplicate = (int) $batch->duplicate_rows;
        $invalid = (int) $batch->invalid_rows;

        $percentage = $total > 0
            ? round(($validated / $total) * 100, 2)
            : 0;

        return [
            'success' => true,
            'status' => $batch->status,
            'total' => $total,
            'validated' => $validated,
            'remaining' => max(0, $total - $validated),
            'ready' => $ready,
            'duplicate' => $duplicate,
            'invalid' => $invalid,
            'percentage' => $percentage,
            'error_message' => $batch->error_message,
        ];
    }

    public function updateValidationRow(
        ImportBatch $batch,
        ImportRow $importRow,
        array $input
    ): array {
        if ($importRow->status === 'imported') {
            throw new \RuntimeException('Data yang sudah diimport tidak dapat diedit dari preview.');
        }

        $rawData = $importRow->raw_data ?? [];
        $rawData['nama'] = $input['name'] ?? null;
        $rawData['email'] = $input['email'] ?? null;
        $rawData['nomor_telepon'] = $input['phone'] ?? null;
        $rawData['nomor_dokumen'] = $input['document_number'] ?? null;
        $rawData['nama_instansi'] = $input['institution_name'] ?? null;
        $rawData['jenis_instansi'] = $input['institution_type'] ?? null;
        $rawData['kota'] = $input['city'] ?? null;
        $rawData['provinsi'] = $input['province'] ?? null;

        $data = $this->normalizeRow($rawData);
        $existingKeys = $this->getExistingCustomerKeys();
        $batchKeys = $batch->rows()
            ->where('status', 'ready')
            ->where('id', '!=', $importRow->id)
            ->whereNotNull('dedupe_key')
            ->pluck('dedupe_key')
            ->flip()
            ->all();

        $analysis = $this->analyzeRow($data, $existingKeys, $batchKeys);
        $oldStatus = $importRow->status;
        $newStatus = $analysis['status'];

        DB::transaction(function () use (
            $batch,
            $importRow,
            $rawData,
            $data,
            $analysis,
            $oldStatus,
            $newStatus,
        ): void {
            $importRow->update([
                'raw_data' => $rawData,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'document_number' => $data['document_number'],
                'institution_name' => $data['institution_name'],
                'institution_type' => $data['institution_type'],
                'normalized_name' => $data['normalized_name'],
                'normalized_email' => $data['normalized_email'],
                'normalized_document_number' => $data['normalized_document_number'],
                'dedupe_key' => $analysis['dedupe_key'],
                'status' => $newStatus,
                'duplicate_reason' => $analysis['duplicate_reason'],
                'error_message' => $analysis['error_message'],
            ]);

            if ($oldStatus !== $newStatus) {
                $counter = [
                    'ready' => 'ready_rows',
                    'duplicate' => 'duplicate_rows',
                    'invalid' => 'invalid_rows',
                ];

                if (isset($counter[$oldStatus])) {
                    $batch->decrement($counter[$oldStatus]);
                }

                if (isset($counter[$newStatus])) {
                    $batch->increment($counter[$newStatus]);
                }
            }
        });

        $batch->refresh();

        if ($batch->rows()->count() >= (int) $batch->total_rows) {
            $batch->update([
                'status' => 'ready',
                'error_message' => null,
            ]);
        }

        return [
            'success' => true,
            'status' => $newStatus,
            'message' => match ($newStatus) {
                'ready' => 'Baris berhasil diperbaiki dan siap diimport.',
                'duplicate' => 'Baris sudah diperbaiki, tetapi terdeteksi sebagai duplicate.',
                default => $analysis['error_message'] ?: 'Baris masih invalid.',
            },
            ...$this->getValidationProgress($batch),
        ];
    }

    private function prepareValidation(ImportBatch $batch): void
    {
        $path = \Storage::disk('local')->path($batch->stored_path);

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        $worksheetInfo = $reader->listWorksheetInfo($path);

        if (count($worksheetInfo) !== 1) {
            throw new \RuntimeException(
                'File Excel harus memiliki tepat satu sheet dengan nama "customers".'
            );
        }

        if (($worksheetInfo[0]['worksheetName'] ?? null) !== 'customers') {
            throw new \RuntimeException('Nama sheet harus "customers".');
        }

        $reader->setReadFilter(
            new CustomerValidationReadFilter(1, 1)
        );

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName('customers');

        if ($sheet === null) {
            throw new \RuntimeException('Sheet customers tidak ditemukan.');
        }

        $headers = $sheet->rangeToArray(
            'A1:H1',
            null,
            true,
            false
        )[0] ?? [];

        $headers = array_map(
            static fn ($value) => trim((string) $value),
            $headers
        );

        $requiredHeaders = [
            'Nama',
            'Email',
            'Nomor Telepon',
            'Nomor Dokumen',
            'Nama Instansi',
            'Jenis Instansi',
            'Kota',
            'Provinsi',
        ];

        if ($headers !== $requiredHeaders) {
            throw new \RuntimeException(
                'Header Excel tidak sesuai standar CRM. Gunakan tombol "Download Template Excel".'
            );
        }

        $totalRows = max(
            0,
            ((int) ($worksheetInfo[0]['totalRows'] ?? 1)) - 1
        );

        $batch->update([
            'status' => 'validating',
            'total_rows' => $totalRows,
            'ready_rows' => 0,
            'duplicate_rows' => 0,
            'invalid_rows' => 0,
            'error_message' => null,
            'completed_at' => null,
        ]);
    }

    private function analyzeRow(
        array $data,
        array $existingKeys,
        array $batchKeys
    ): array {
        $status = 'ready';
        $duplicateReason = null;
        $errorMessage = null;

        if ($data['name'] === null) {
            $status = 'invalid';
            $errorMessage = 'Nama customer wajib diisi.';
        }

        if (
            $status === 'ready'
            && $data['email'] !== null
            && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $status = 'invalid';
            $errorMessage = 'Format email tidak valid.';
        }

        if (
            $status === 'ready'
            && $data['institution_type'] === null
            && $data['institution_name'] !== null
        ) {
            $status = 'invalid';
            $errorMessage = 'Jenis instansi wajib diisi jika Nama Instansi diisi.';
        }

        if (
            $status === 'ready'
            && $data['institution_type'] !== null
            && !isset(self::ALLOWED_INSTITUTION_TYPES[$data['institution_type']])
        ) {
            $status = 'invalid';
            $errorMessage = 'Jenis instansi tidak sesuai format standar CRM.';
        }

        if ($status === 'ready') {
            $duplicateReason = $this->findDuplicateReason(
                $data,
                $existingKeys,
                $batchKeys
            );

            if ($duplicateReason !== null) {
                $status = 'duplicate';
            }
        }

        return [
            'status' => $status,
            'duplicate_reason' => $duplicateReason,
            'error_message' => $errorMessage,
            'dedupe_key' => $this->buildDedupeKey($data),
        ];
    }

    public function processSheet(
        ImportBatch $batch,
        string $sheetName,
        Collection $rows
    ): void {
        $existingKeys =
            $this->getExistingCustomerKeys();

        $batchKeys = [];

        foreach ($rows as $index => $row) {
            $rowNumber =
                $index + 2;

            $data =
                $this->normalizeRow(
                    $row->toArray()
                );

            $status = 'ready';

            $duplicateReason = null;

            $errorMessage = null;

            if (
                $data['name']
                === null
            ) {
                $status =
                    'invalid';

                $errorMessage =
                    'Nama customer wajib diisi.';
            }

            if (
                $status === 'ready'
                && $data['email'] !== null
                && !filter_var(
                    $data['email'],
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $status =
                    'invalid';

                $errorMessage =
                    'Format email tidak valid.';
            }

            if (
                $status === 'ready'
                && $data['institution_type'] === null
                && $data['institution_name'] !== null
            ) {
                $status =
                    'invalid';

                $errorMessage =
                    'Jenis instansi wajib diisi jika Nama Instansi diisi.';
            }

            if (
                $status === 'ready'
                && $data['institution_type'] !== null
                && !isset(
                    self::ALLOWED_INSTITUTION_TYPES[
                        $data['institution_type']
                    ]
                )
            ) {
                $status =
                    'invalid';

                $errorMessage =
                    'Jenis instansi tidak sesuai format standar CRM.';
            }

            if ($status === 'ready') {
                $duplicateReason =
                    $this->findDuplicateReason(
                        $data,
                        $existingKeys,
                        $batchKeys
                    );

                if (
                    $duplicateReason !== null
                ) {
                    $status =
                        'duplicate';
                }
            }

            $dedupeKey =
                $this->buildDedupeKey(
                    $data
                );

            ImportRow::create([
                'import_batch_id' =>
                    $batch->id,

                'sheet_name' =>
                    $sheetName,

                'row_number' =>
                    $rowNumber,

                'raw_data' =>
                    $row->toArray(),

                'name' =>
                    $data['name'],

                'email' =>
                    $data['email'],

                'phone' =>
                    $data['phone'],

                'document_number' =>
                    $data['document_number'],

                'institution_name' =>
                    $data['institution_name'],

                'institution_type' =>
                    $data['institution_type'],

                'normalized_name' =>
                    $data['normalized_name'],

                'normalized_email' =>
                    $data['normalized_email'],

                'normalized_document_number' =>
                    $data['normalized_document_number'],

                'dedupe_key' =>
                    $dedupeKey,

                'status' =>
                    $status,

                'duplicate_reason' =>
                    $duplicateReason,

                'error_message' =>
                    $errorMessage,
            ]);

            $batch->increment(
                'total_rows'
            );

            if (
                $status === 'ready'
            ) {
                $batch->increment(
                    'ready_rows'
                );

                if (
                    $dedupeKey !== null
                ) {
                    $batchKeys[
                        $dedupeKey
                    ] = true;
                }
            }

            if (
                $status === 'duplicate'
            ) {
                $batch->increment(
                    'duplicate_rows'
                );
            }

            if (
                $status === 'invalid'
            ) {
                $batch->increment(
                    'invalid_rows'
                );
            }
        }
    }

    /**
     * Kompatibilitas dengan pemanggilan lama.
     *
     * Sekarang execute() hanya memproses satu chunk.
     */
    public function execute(
        ImportBatch $batch
    ): array {
        return $this->executeChunk(
            $batch,
            25
        );
    }

    /**
     * Memproses satu chunk kecil.
     *
     * Satu chunk = satu database transaction.
     * Row yang sudah imported tidak pernah diproses ulang.
     */
    public function executeChunk(
        ImportBatch $batch,
        int $chunkSize = 25
    ): array {
        $chunkSize =
            max(
                1,
                min(
                    $chunkSize,
                    50
                )
            );

        $importedThisChunk = 0;

        DB::transaction(
            function () use (
                $batch,
                $chunkSize,
                &$importedThisChunk
            ): void {
                $institutionMap =
                    $this->getInstitutionMap();

                $rows =
                    $batch
                        ->rows()
                        ->where(
                            'status',
                            'ready'
                        )
                        ->orderBy(
                            'id'
                        )
                        ->lockForUpdate()
                        ->limit(
                            $chunkSize
                        )
                        ->get();

                if (
                    $rows->isEmpty()
                ) {
                    return;
                }

                $batch->update([
                    'status' =>
                        'processing',

                    'error_message' =>
                        null,
                ]);

                foreach (
                    $rows as $importRow
                ) {
                    $institutionId =
                        null;

                    if (
                        $importRow
                            ->institution_name
                            !== null
                    ) {
                        $institutionId =
                            $this->resolveInstitution(
                                $importRow
                                    ->institution_name,

                                $importRow
                                    ->institution_type,

                                $institutionMap
                            );
                    }

                    $customer =
                        Customer::create([
                            'customer_code' =>
                                'TEMP-'
                                . Str::uuid(),

                            'institution_id' =>
                                $institutionId,

                            'name' =>
                                $importRow
                                    ->name,

                            'email' =>
                                $importRow
                                    ->email,

                            'phone' =>
                                $importRow
                                    ->phone,

                            'document_number' =>
                                $importRow
                                    ->document_number,

                            'city' =>
                                $importRow
                                    ->raw_data['kota']
                                ?? null,

                            'province' =>
                                $importRow
                                    ->raw_data['provinsi']
                                ?? null,

                            'status' =>
                                'prospect',

                            'source' =>
                                'excel',

                            'notes' =>
                                'Imported from '
                                . $batch
                                    ->original_filename
                                . ' / sheet '
                                . $importRow
                                    ->sheet_name,
                        ]);

                    $customer->update([
                        'customer_code' =>
                            sprintf(
                                'CUS-%06d',
                                $customer->id
                            ),
                    ]);

                    $importRow->update([
                        'status' =>
                            'imported',
                    ]);

                    $importedThisChunk++;
                }
            }
        );

        $batch->refresh();

        $remaining =
            $batch
                ->rows()
                ->where(
                    'status',
                    'ready'
                )
                ->count();

        if (
            $remaining === 0
        ) {
            $batch->update([
                'status' =>
                    'completed',

                'completed_at' =>
                    $batch->completed_at
                        ?? now(),

                'error_message' =>
                    null,
            ]);
        } else {
            $batch->update([
                'status' =>
                    'processing',
            ]);
        }

        return [
            ...$this->getProgress(
                $batch
            ),

            'success' =>
                true,

            'chunk_size' =>
                $chunkSize,

            'chunk_processed' =>
                $importedThisChunk,

            'remaining' =>
                $remaining,

            'message' =>
                $remaining === 0
                    ? 'Semua data ready sudah selesai diimport.'
                    : sprintf(
                        '%d customer diproses pada chunk ini. %d masih tersisa.',
                        $importedThisChunk,
                        $remaining
                    ),
        ];
    }

    /**
     * Mengambil status progress aktual dari database.
     */
    public function getProgress(
        ImportBatch $batch
    ): array {
        $batch->refresh();

        $imported =
            $batch
                ->rows()
                ->where(
                    'status',
                    'imported'
                )
                ->count();

        $ready =
            $batch
                ->rows()
                ->where(
                    'status',
                    'ready'
                )
                ->count();

        $duplicate =
            $batch
                ->rows()
                ->where(
                    'status',
                    'duplicate'
                )
                ->count();

        $invalid =
            $batch
                ->rows()
                ->where(
                    'status',
                    'invalid'
                )
                ->count();

        $total =
            (int) $batch->total_rows;

        $readyTotal =
            (int) $batch->ready_rows;

        $processed =
            $imported
            + $duplicate
            + $invalid;

        $percentage =
            $readyTotal > 0
                ? round(
                    (
                        $imported
                        / $readyTotal
                    ) * 100,
                    2
                )
                : (
                    $batch->status === 'completed'
                        ? 100
                        : 0
                );

        $overallPercentage =
            $total > 0
                ? round(
                    (
                        $processed
                        / $total
                    ) * 100,
                    2
                )
                : 0;

        return [
            'batch_id' =>
                $batch->id,

            'status' =>
                $batch->status,

            'total' =>
                $total,

            'ready_total' =>
                $readyTotal,

            'imported' =>
                $imported,

            'remaining' =>
                $ready,

            'duplicate' =>
                $duplicate,

            'invalid' =>
                $invalid,

            'processed' =>
                $processed,

            'percentage' =>
                min(
                    100,
                    $percentage
                ),

            'overall_percentage' =>
                min(
                    100,
                    $overallPercentage
                ),

            'completed_at' =>
                $batch
                    ->completed_at
                    ?->toISOString(),

            'error_message' =>
                $batch
                    ->error_message,
        ];
    }

    private function normalizeRow(
        array $row
    ): array {
        $name =
            $this->cleanString(
                $row['nama'] ?? null
            );

        $email =
            $this->cleanEmail(
                $row['email'] ?? null
            );

        $phone =
            $this->extractPhone(
                $row['nomor_telepon'] ?? null
            );

        $documentNumber =
            $this->cleanString(
                $row['nomor_dokumen']
                    ?? null
            );

        $institutionName =
            $this->cleanString(
                $row['nama_instansi']
                    ?? null
            );

        $institutionType =
            $this->cleanString(
                $row['jenis_instansi']
                    ?? null
            );

        return [
            'name' =>
                $name,

            'email' =>
                $email,

            'phone' =>
                $phone,

            'document_number' =>
                $documentNumber,

            'institution_name' =>
                $institutionName,

            'institution_type' =>
                $institutionType,

            'normalized_name' =>
                $this->normalizeForComparison(
                    $name
                ),

            'normalized_email' =>
                $this->normalizeEmail(
                    $email
                ),

            'normalized_document_number' =>
                $this->normalizeForComparison(
                    $documentNumber
                ),
        ];
    }

    private function cleanString(
        mixed $value
    ): ?string {
        if (
            $value === null
        ) {
            return null;
        }

        $value =
            trim(
                (string) $value
            );

        if (
            $value === ''
        ) {
            return null;
        }

        $lower =
            Str::lower(
                $value
            );

        if (
            in_array(
                $lower,
                [
                    'tidak ada',
                    'n/a',
                    'na',
                    'null',
                    '-',
                ],
                true
            )
        ) {
            return null;
        }

        return preg_replace(
            '/\s+/',
            ' ',
            $value
        );
    }

    private function cleanEmail(
        mixed $value
    ): ?string {
        $value =
            $this->cleanString(
                $value
            );

        if (
            $value === null
        ) {
            return null;
        }

        return Str::lower(
            trim(
                $value
            )
        );
    }

    private function extractPhone(
        mixed $value
    ): ?string {
        $value =
            $this->cleanString(
                $value
            );

        if (
            $value === null
        ) {
            return null;
        }

        $value =
            preg_replace(
                '/[^\d+]/',
                '',
                $value
            );

        if (
            $value === null
            || strlen($value) < 8
        ) {
            return null;
        }

        return $value;
    }

    private function normalizeEmail(
        ?string $value
    ): ?string {
        if (
            $value === null
        ) {
            return null;
        }

        return Str::lower(
            trim(
                $value
            )
        );
    }

    private function normalizeForComparison(
        ?string $value
    ): ?string {
        if (
            $value === null
        ) {
            return null;
        }

        $value =
            Str::lower(
                trim(
                    $value
                )
            );

        $value =
            preg_replace(
                '/[^a-z0-9]+/u',
                ' ',
                $value
            );

        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                $value
            )
        );
    }

    private function buildDedupeKey(
        array $data
    ): ?string {
        if (
            $data[
                'normalized_document_number'
            ] !== null
        ) {
            return 'doc:'
                . $data[
                    'normalized_document_number'
                ];
        }

        if (
            $data['normalized_name']
                !== null
            && $data['institution_name']
                !== null
        ) {
            return 'name_institution:'
                . $data[
                    'normalized_name'
                ]
                . '|'
                . $this->normalizeForComparison(
                    $data[
                        'institution_name'
                    ]
                );
        }

        if (
            $data['normalized_name']
                !== null
            && $data['normalized_email']
                !== null
        ) {
            return 'name_email:'
                . $data[
                    'normalized_name'
                ]
                . '|'
                . $data[
                    'normalized_email'
                ];
        }

        return null;
    }

    private function findDuplicateReason(
        array $data,
        array $existingKeys,
        array $batchKeys
    ): ?string {
        $dedupeKey =
            $this->buildDedupeKey(
                $data
            );

        if (
            $dedupeKey !== null
            && isset(
                $batchKeys[
                    $dedupeKey
                ]
            )
        ) {
            return 'Duplikat pada file Excel yang sama.';
        }

        if (
            $data[
                'normalized_document_number'
            ] !== null
            && isset(
                $existingKeys[
                    'doc:'
                    . $data[
                        'normalized_document_number'
                    ]
                ]
            )
        ) {
            return 'Nomor dokumen sudah ada di CRM.';
        }

        if (
            $data['normalized_email']
                !== null
            && $data['normalized_name']
                !== null
            && isset(
                $existingKeys[
                    'name_email:'
                    . $data[
                        'normalized_name'
                    ]
                    . '|'
                    . $data[
                        'normalized_email'
                    ]
                ]
            )
        ) {
            return 'Nama dan email sudah ada di CRM.';
        }

        if (
            $data['normalized_name']
                !== null
            && $data['institution_name']
                !== null
        ) {
            $key =
                'name_institution:'
                . $data[
                    'normalized_name'
                ]
                . '|'
                . $this->normalizeForComparison(
                    $data[
                        'institution_name'
                    ]
                );

            if (
                isset(
                    $existingKeys[
                        $key
                    ]
                )
            ) {
                return 'Nama dan instansi sudah ada di CRM.';
            }
        }

        return null;
    }

    private function getExistingCustomerKeys(): array
    {
        $keys = [];

        Customer::query()
            ->select([
                'id',
                'name',
                'email',
                'document_number',
                'institution_id',
            ])
            ->with(
                'institution:id,name'
            )
            ->chunkById(
                500,
                function ($customers)
                use (&$keys) {
                    foreach (
                        $customers
                        as $customer
                    ) {
                        $normalizedName =
                            $this
                                ->normalizeForComparison(
                                    $customer
                                        ->name
                                );

                        $normalizedEmail =
                            $this
                                ->normalizeEmail(
                                    $customer
                                        ->email
                                );

                        $normalizedDocument =
                            $this
                                ->normalizeForComparison(
                                    $customer
                                        ->document_number
                                );

                        if (
                            $normalizedDocument
                            !== null
                        ) {
                            $keys[
                                'doc:'
                                . $normalizedDocument
                            ] = true;
                        }

                        if (
                            $normalizedName
                            !== null
                            && $normalizedEmail
                            !== null
                        ) {
                            $keys[
                                'name_email:'
                                . $normalizedName
                                . '|'
                                . $normalizedEmail
                            ] = true;
                        }

                        if (
                            $normalizedName
                            !== null
                            && $customer
                                ->institution
                        ) {
                            $keys[
                                'name_institution:'
                                . $normalizedName
                                . '|'
                                . $this
                                    ->normalizeForComparison(
                                        $customer
                                            ->institution
                                            ->name
                                    )
                            ] = true;
                        }
                    }
                }
            );

        return $keys;
    }

    private function getInstitutionMap(): array
    {
        $map = [];

        Institution::query()
            ->select([
                'id',
                'name',
            ])
            ->get()
            ->each(
                function ($institution)
                use (&$map) {
                    $key =
                        $this
                            ->normalizeForComparison(
                                $institution
                                    ->name
                            );

                    if (
                        $key !== null
                    ) {
                        $map[
                            $key
                        ] =
                            $institution
                                ->id;
                    }
                }
            );

        return $map;
    }

    private function resolveInstitution(
        string $institutionName,
        ?string $institutionType,
        array &$institutionMap
    ): ?int {
        $normalizedName =
            $this
                ->normalizeForComparison(
                    $institutionName
                );

        if (
            $normalizedName === null
        ) {
            return null;
        }

        if (
            isset(
                $institutionMap[
                    $normalizedName
                ]
            )
        ) {
            return $institutionMap[
                $normalizedName
            ];
        }

        $type =
            self::ALLOWED_INSTITUTION_TYPES[
                $institutionType
            ] ?? 'other';

        $institution =
            Institution::create([
                'name' =>
                    $institutionName,

                'type' =>
                    $type,
            ]);

        $institutionMap[
            $normalizedName
        ] =
            $institution->id;

        return $institution->id;
    }
}
