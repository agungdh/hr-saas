<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveQuota extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveQuotaFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'year',
        'total_days',
        'used_days',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'year' => 'integer',
            'total_days' => 'integer',
            'used_days' => 'integer',
        ];
    }

    /**
     * Get the employee that owns the leave quota.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the remaining leave days.
     */
    public function getRemainingDays(): int
    {
        return max(0, $this->total_days - $this->used_days);
    }

    /**
     * Check if the employee has enough leave days.
     */
    public function hasEnoughDays(int $days): bool
    {
        return $this->getRemainingDays() >= $days;
    }
}
