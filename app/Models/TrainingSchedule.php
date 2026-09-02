<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TrainingSchedule extends Model {
    use HasFactory;
    protected $fillable = ['training_id','trainer_id','training_date','start_time','end_time','location','capacity','status','notes'];
    protected function casts(): array { return ['training_date'=>'date']; }
    public function training(): BelongsTo { return $this->belongsTo(Training::class); }
    public function trainer(): BelongsTo { return $this->belongsTo(Trainer::class); }
    public function registrations(): HasMany { return $this->hasMany(Registration::class); }
}
