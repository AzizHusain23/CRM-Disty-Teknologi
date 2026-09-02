<?php

namespace App\Imports;

use App\Models\ImportBatch;
use App\Services\CustomerImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Import;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerExcelImport implements
    Import,
    ToCollection,
    WithHeadingRow
{
    public function __construct(
        private readonly ImportBatch $batch,
        private readonly CustomerImportService $service
    ) {
    }

    public function collection(
        Collection $rows
    ): void {
        $this->service->processSheet(
            $this->batch,
            'customers',
            $rows
        );
    }
}