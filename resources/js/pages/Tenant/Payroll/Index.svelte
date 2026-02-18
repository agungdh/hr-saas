<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Link, router } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { DollarSign, CheckCircle, Clock } from 'lucide-svelte';

    let {
        periods,
    }: {
        periods: {
            data: Array<{
                id: number;
                month: number;
                year: number;
                start_date: string;
                end_date: string;
                is_processed: boolean;
                payrolls_count: number;
            }>;
            links: Array<{ url: string | null; label: string; active: boolean }>;
        };
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Payroll', href: '/payroll' },
    ];

    const flash = $derived($page.props.flash as { success?: string; error?: string } | undefined);

    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;

    let generateMonth = $state(currentMonth.toString());
    let generateYear = $state(currentYear.toString());
    let isGenerating = $state(false);

    function generatePayroll(): void {
        isGenerating = true;
        router.post('/payroll/generate', {
            month: parseInt(generateMonth),
            year: parseInt(generateYear),
        }, {
            onFinish: () => isGenerating = false,
        });
    }

    function getMonthName(month: number): string {
        return new Date(2000, month - 1).toLocaleString('default', { month: 'long' });
    }
</script>

<AppHead title="Payroll" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Payroll Management</h1>
                <p class="text-sm text-muted-foreground">Generate and manage payroll periods</p>
            </div>
        </div>

        {#if flash?.success}
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200">
                {flash.success}
            </div>
        {/if}

        {#if flash?.error}
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
                {flash.error}
            </div>
        {/if}

        <!-- Generate Payroll Card -->
        <Card>
            <CardHeader>
                <CardTitle class="text-lg">Generate Payroll</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="flex flex-wrap items-end gap-4">
                    <div class="space-y-2">
                        <Label for="month">Month</Label>
                        <select
                            id="month"
                            bind:value={generateMonth}
                            class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            {#each Array.from({ length: 12 }, (_, i) => i + 1) as month}
                                <option value={month}>{getMonthName(month)}</option>
                            {/each}
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="year">Year</Label>
                        <Input
                            id="year"
                            type="number"
                            bind:value={generateYear}
                            min="2020"
                            max="2100"
                            class="w-24"
                        />
                    </div>

                    <Button onclick={generatePayroll} disabled={isGenerating}>
                        {#if isGenerating}
                            Generating...
                        {:else}
                            Generate Payroll
                        {/if}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Payroll Periods Table -->
        <div class="rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Period</th>
                            <th class="px-4 py-3 text-left font-medium">Employees</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each periods.data as period}
                            <tr class="border-b border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{getMonthName(period.month)} {period.year}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {period.payrolls_count} employees
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {#if period.is_processed}
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <CheckCircle class="h-3 w-3" />
                                            Processed
                                        </span>
                                    {:else}
                                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <Clock class="h-3 w-3" />
                                            Pending
                                        </span>
                                    {/if}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {#if period.is_processed}
                                        <Button variant="outline" size="sm" href="/payroll/{period.id}" as={Link}>
                                            View Details
                                        </Button>
                                    {:else}
                                        <Button size="sm" href="/payroll/{period.id}" as={Link}>
                                            Process
                                        </Button>
                                    {/if}
                                </td>
                            </tr>
                        {/each}
                        {#if periods.data.length === 0}
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                                    No payroll periods found. Generate your first payroll above.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AppLayout>
