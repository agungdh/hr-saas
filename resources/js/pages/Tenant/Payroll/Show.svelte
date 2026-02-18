<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Link } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Users, DollarSign, TrendingDown, TrendingUp } from 'lucide-svelte';

    let {
        period,
        payrolls,
        stats,
    }: {
        period: {
            id: number;
            month: number;
            year: number;
            start_date: string;
            end_date: string;
            is_processed: boolean;
        };
        payrolls: {
            data: Array<{
                id: number;
                base_salary: number;
                tax_deduction: number;
                leave_deduction: number;
                allowances: number;
                net_salary: number;
                employee: {
                    name: string;
                    department: { name: string } | null;
                };
            }>;
        };
        stats: {
            total_employees: number;
            total_base_salary: number;
            total_tax_deduction: number;
            total_leave_deduction: number;
            total_allowances: number;
            total_net_salary: number;
        };
    } = $props();

    function getMonthName(month: number): string {
        return new Date(2000, month - 1).toLocaleString('default', { month: 'long' });
    }

    function formatCurrency(amount: number): string {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
    }

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Payroll', href: '/payroll' },
        { title: `${getMonthName(period.month)} ${period.year}`, href: `/payroll/${period.id}` },
    ];
</script>

<AppHead title="Payroll - {getMonthName(period.month)} {period.year}" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">{getMonthName(period.month)} {period.year}</h1>
                <p class="text-sm text-muted-foreground">Payroll details for this period</p>
            </div>
            <Button variant="outline" asChild>
                {#snippet children(props)}
                    <Link {...props} href="/payroll">Back to Payroll</Link>
                {/snippet}
            </Button>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Employees</CardTitle>
                    <Users class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{stats.total_employees}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Total Base</CardTitle>
                    <DollarSign class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-lg font-bold">{formatCurrency(stats.total_base_salary)}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Tax Deduction</CardTitle>
                    <TrendingDown class="h-4 w-4 text-red-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-lg font-bold text-red-600">{formatCurrency(stats.total_tax_deduction)}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Leave Deduction</CardTitle>
                    <TrendingDown class="h-4 w-4 text-red-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-lg font-bold text-red-600">{formatCurrency(stats.total_leave_deduction)}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Net Total</CardTitle>
                    <TrendingUp class="h-4 w-4 text-green-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-lg font-bold text-green-600">{formatCurrency(stats.total_net_salary)}</div>
                </CardContent>
            </Card>
        </div>

        <!-- Payroll Table -->
        <div class="rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Employee</th>
                            <th class="px-4 py-3 text-right font-medium">Base Salary</th>
                            <th class="px-4 py-3 text-right font-medium">Tax</th>
                            <th class="px-4 py-3 text-right font-medium">Leave Ded.</th>
                            <th class="px-4 py-3 text-right font-medium">Allowances</th>
                            <th class="px-4 py-3 text-right font-medium">Net Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each payrolls.data as payroll}
                            <tr class="border-b border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{payroll.employee.name}</div>
                                    <div class="text-xs text-muted-foreground">{payroll.employee.department?.name || 'No Department'}</div>
                                </td>
                                <td class="px-4 py-3 text-right">{formatCurrency(payroll.base_salary)}</td>
                                <td class="px-4 py-3 text-right text-red-600">-{formatCurrency(payroll.tax_deduction)}</td>
                                <td class="px-4 py-3 text-right text-red-600">-{formatCurrency(payroll.leave_deduction)}</td>
                                <td class="px-4 py-3 text-right text-green-600">+{formatCurrency(payroll.allowances)}</td>
                                <td class="px-4 py-3 text-right font-bold">{formatCurrency(payroll.net_salary)}</td>
                            </tr>
                        {/each}
                        {#if payrolls.data.length === 0}
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                    No payroll records found.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AppLayout>
