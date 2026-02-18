<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    /** @use HasFactory<\Database\Factories\PayrollPeriodFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'month',
        'year',
        'start_date',
        'end_date',
        'is_processed',
        'processed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_processed' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Get the payrolls for the payroll period.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Get the period label (e.g., "January 2026").
     */
    public function getLabel(): string
    {
        return $this->start_date->format('F Y');
    }
}
