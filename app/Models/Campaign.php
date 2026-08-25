<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'body',
        'status',
    ];

    // Campaign ini milik 1 User (Admin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1 Campaign memiliki banyak log pengiriman email
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }
}
