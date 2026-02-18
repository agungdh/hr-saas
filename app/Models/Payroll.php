<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    /** @use HasFactory<\Database\Factories\PayrollFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'base_salary',
        'tax_deduction',
        'leave_deduction',
        'allowances',
        'net_salary',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'base_salary' => 'integer',
            'tax_deduction' => 'integer',
            'leave_deduction' => 'integer',
            'allowances' => 'integer',
            'net_salary' => 'integer',
        ];
    }

    /**
     * Get the employee that owns the payroll.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the payroll period that owns the payroll.
     */
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    /**
     * Format salary as currency.
     */
    public function formatAmount(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
