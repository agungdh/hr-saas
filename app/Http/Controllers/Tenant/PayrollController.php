<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PayrollGenerateRequest;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function index(): Response
    {
        $periods = PayrollPeriod::withCount('payrolls')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        return Inertia::render('Tenant/Payroll/Index', [
            'periods' => $periods,
        ]);
    }

    public function show(PayrollPeriod $period): Response
    {
        $payrolls = Payroll::with('employee.department')
            ->where('payroll_period_id', $period->id)
            ->orderBy('created_at')
            ->paginate(20);

        $stats = $this->payrollService->getPayrollStats($period);

        return Inertia::render('Tenant/Payroll/Show', [
            'period' => $period,
            'payrolls' => $payrolls,
            'stats' => $stats,
        ]);
    }

    public function generate(PayrollGenerateRequest $request): RedirectResponse
    {
        $month = $request->month;
        $year = $request->year;

        // Check if period already exists
        $existingPeriod = PayrollPeriod::where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existingPeriod !== null) {
            if ($existingPeriod->is_processed) {
                return redirect()->route('tenant.payroll.index')
                    ->with('error', 'Payroll for this period has already been generated.');
            }

            $count = $this->payrollService->generatePayrollForPeriod($existingPeriod);

            return redirect()->route('tenant.payroll.show', $existingPeriod)
                ->with('success', "Payroll generated for {$count} employees.");
        }

        // Create new period
        $period = $this->payrollService->createPayrollPeriod($month, $year);
        $count = $this->payrollService->generatePayrollForPeriod($period);

        return redirect()->route('tenant.payroll.show', $period)
            ->with('success', "Payroll generated for {$count} employees.");
    }
}
