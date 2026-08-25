<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomer_dok',
        'nama',
        'institusi',
        'email_primary',
        'phone',
        'status',
    ];

    // 1 Lead bisa memiliki banyak log pengiriman email
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
