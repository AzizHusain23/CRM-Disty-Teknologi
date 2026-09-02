<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_batch_id',
        'sheet_name',
        'row_number',
        'raw_data',
        'name',
        'email',
        'phone',
        'document_number',
        'institution_name',
        'institution_type',
        'normalized_name',
        'normalized_email',
        'normalized_document_number',
        'dedupe_key',
        'status',
        'duplicate_reason',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(
            ImportBatch::class,
            'import_batch_id'
        );
    }
}