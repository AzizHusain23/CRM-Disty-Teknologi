<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Trainer extends Model {
    use HasFactory;
    protected $fillable = ['name','email','phone','notes','is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function schedules(): HasMany { return $this->hasMany(TrainingSchedule::class); }
}
