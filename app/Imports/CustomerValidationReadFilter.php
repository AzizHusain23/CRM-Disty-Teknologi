<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class CustomerValidationReadFilter implements IReadFilter
{
    public function __construct(
        private readonly int $startRow,
        private readonly int $endRow,
    ) {
    }

    public function readCell(
        $column,
        $row,
        $worksheetName = ''
    ): bool {
        return $row === 1
            || ($row >= $this->startRow && $row <= $this->endRow);
    }
}
