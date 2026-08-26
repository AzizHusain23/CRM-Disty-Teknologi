<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Models\Institution;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function processSheet(
        ImportBatch $batch,
        string $sheetName,
        Collection $rows
    ): void {
        $existingKeys = $this->getExistingCustomerKeys();

        $batchKeys = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $data = $this->normalizeRow(
                $row->toArray()
            );

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
                && !filter_var(
                    $data['email'],
                    FILTER_VALIDATE_EMAIL
                )
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
                $status = 'invalid';

                $errorMessage =
                    'Jenis instansi tidak sesuai format standar CRM.';
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

            $dedupeKey = $this->buildDedupeKey(
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

            if ($status === 'ready') {
                $batch->increment(
                    'ready_rows'
                );

                if ($dedupeKey !== null) {
                    $batchKeys[$dedupeKey] = true;
                }
            }

            if ($status === 'duplicate') {
                $batch->increment(
                    'duplicate_rows'
                );
            }

            if ($status === 'invalid') {
                $batch->increment(
                    'invalid_rows'
                );
            }
        }
    }

    public function execute(
        ImportBatch $batch
    ): void {
        DB::transaction(function () use ($batch) {
            $institutionMap =
                $this->getInstitutionMap();

            $batch->update([
                'status' => 'processing',
                'error_message' => null,
            ]);

            $batch
                ->rows()
                ->where('status', 'ready')
                ->orderBy('id')
                ->chunkById(
                    250,
                    function (Collection $rows)
                    use (&$institutionMap) {

                        foreach ($rows as $importRow) {

                            $institutionId = null;

                            if (
                                $importRow->institution_name
                                !== null
                            ) {
                                $institutionId =
                                    $this->resolveInstitution(
                                        $importRow->institution_name,
                                        $importRow->institution_type,
                                        $institutionMap
                                    );
                            }

                            $customer =
                                Customer::create([
                                    'customer_code' =>
                                        'TEMP-' . Str::uuid(),

                                    'institution_id' =>
                                        $institutionId,

                                    'name' =>
                                        $importRow->name,

                                    'email' =>
                                        $importRow->email,

                                    'phone' =>
                                        $importRow->phone,

                                    'document_number' =>
                                        $importRow->document_number,

                                    'city' =>
                                        $importRow->raw_data['kota']
                                        ?? null,

                                    'province' =>
                                        $importRow->raw_data['provinsi']
                                        ?? null,

                                    'status' =>
                                        'active',

                                    'source' =>
                                        'excel',

                                    'notes' =>
                                        'Imported from '
                                        . $importRow->batch->original_filename
                                        . ' / sheet '
                                        . $importRow->sheet_name,
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
                        }
                    }
                );

            $batch->update([
                'status' =>
                    'completed',

                'completed_at' =>
                    now(),
            ]);
        });
    }

    private function normalizeRow(
        array $row
    ): array {
        $name = $this->cleanString(
            $row['nama'] ?? null
        );

        $email = $this->cleanEmail(
            $row['email'] ?? null
        );

        $phone = $this->extractPhone(
            $row['nomor_telepon'] ?? null
        );

        $documentNumber =
            $this->cleanString(
                $row['nomor_dokumen'] ?? null
            );

        $institutionName =
            $this->cleanString(
                $row['nama_instansi'] ?? null
            );

        $institutionType =
            $this->cleanString(
                $row['jenis_instansi'] ?? null
            );

        return [
            'name' => $name,

            'email' => $email,

            'phone' => $phone,

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
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $lower =
            Str::lower($value);

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
            $this->cleanString($value);

        if ($value === null) {
            return null;
        }

        if (
            filter_var(
                $value,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            return Str::lower(
                trim($value)
            );
        }

        return null;
    }

    private function extractPhone(
        mixed $value
    ): ?string {
        $value =
            $this->cleanString($value);

        if ($value === null) {
            return null;
        }

        $value = preg_replace(
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
        if ($value === null) {
            return null;
        }

        return Str::lower(
            trim($value)
        );
    }

    private function normalizeForComparison(
        ?string $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value =
            Str::lower(
                trim($value)
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
            $data['normalized_document_number']
            !== null
        ) {
            return 'doc:'
                . $data[
                    'normalized_document_number'
                ];
        }

        if (
            $data['normalized_name'] !== null
            && $data['institution_name'] !== null
        ) {
            return 'name_institution:'
                . $data['normalized_name']
                . '|'
                . $this->normalizeForComparison(
                    $data['institution_name']
                );
        }

        if (
            $data['normalized_name'] !== null
            && $data['normalized_email'] !== null
        ) {
            return 'name_email:'
                . $data['normalized_name']
                . '|'
                . $data['normalized_email'];
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
                $batchKeys[$dedupeKey]
            )
        ) {
            return 'Duplikat pada file Excel yang sama.';
        }

        if (
            $data['normalized_document_number']
            !== null
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
            $data['normalized_email'] !== null
            && $data['normalized_name'] !== null
            && isset(
                $existingKeys[
                    'name_email:'
                    . $data['normalized_name']
                    . '|'
                    . $data['normalized_email']
                ]
            )
        ) {
            return 'Nama dan email sudah ada di CRM.';
        }

        if (
            $data['normalized_name'] !== null
            && $data['institution_name'] !== null
        ) {
            $key =
                'name_institution:'
                . $data['normalized_name']
                . '|'
                . $this->normalizeForComparison(
                    $data['institution_name']
                );

            if (
                isset(
                    $existingKeys[$key]
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
            ->with('institution:id,name')
            ->chunkById(
                500,
                function ($customers)
                use (&$keys) {

                    foreach (
                        $customers as $customer
                    ) {
                        $normalizedName =
                            $this->normalizeForComparison(
                                $customer->name
                            );

                        $normalizedEmail =
                            $this->normalizeEmail(
                                $customer->email
                            );

                        $normalizedDocument =
                            $this->normalizeForComparison(
                                $customer->document_number
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
                            && $customer->institution
                        ) {
                            $keys[
                                'name_institution:'
                                . $normalizedName
                                . '|'
                                . $this->normalizeForComparison(
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
                        $this->normalizeForComparison(
                            $institution->name
                        );

                    if ($key !== null) {
                        $map[$key] =
                            $institution->id;
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
            $this->normalizeForComparison(
                $institutionName
            );

        if ($normalizedName === null) {
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
        ] = $institution->id;

        return $institution->id;
    }
}