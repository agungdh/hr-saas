<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Link } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Users, UserCheck, Clock, DollarSign, AlertTriangle } from 'lucide-svelte';

    let {
        stats,
        limitInfo,
        recentLeaveRequests,
    }: {
        stats: {
            total_employees: number;
            active_employees: number;
            pending_leave_requests: number;
            unprocessed_payrolls: number;
        };
        limitInfo: {
            plan: string;
            current_count: number;
            limit: number | string;
            remaining: number | string;
            is_unlimited: boolean;
            can_add_more: boolean;
        };
        recentLeaveRequests: Array<{
            id: number;
            employee: { name: string };
            start_date: string;
            end_date: string;
            days_count: number;
            reason: string;
        }>;
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
    ];

    const flash = $derived($page.props.flash as { success?: string; error?: string } | undefined);

    function formatCurrency(amount: number): string {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
    }
</script>

<AppHead title="Dashboard" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 p-6">
        <!-- Flash Messages -->
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

        <!-- Limit Warning -->
        {#if !limitInfo.is_unlimited && !limitInfo.can_add_more}
            <div class="flex items-center gap-3 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-950">
                <AlertTriangle class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                <div>
                    <p class="font-medium text-yellow-800 dark:text-yellow-200">Employee Limit Reached</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">Upgrade to Pro for unlimited employees.</p>
                </div>
            </div>
        {/if}

        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Total Employees</CardTitle>
                    <Users class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{stats.total_employees}</div>
                    <p class="text-xs text-muted-foreground">
                        {#if limitInfo.is_unlimited}
                            Unlimited plan
                        {:else}
                            {limitInfo.remaining} remaining
                        {/if}
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Active Employees</CardTitle>
                    <UserCheck class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{stats.active_employees}</div>
                    <p class="text-xs text-muted-foreground">Currently working</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Pending Leave</CardTitle>
                    <Clock class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{stats.pending_leave_requests}</div>
                    <p class="text-xs text-muted-foreground">Awaiting approval</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Unprocessed Payroll</CardTitle>
                    <DollarSign class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{stats.unprocessed_payrolls}</div>
                    <p class="text-xs text-muted-foreground">Periods pending</p>
                </CardContent>
            </Card>
        </div>

        <!-- Quick Actions & Recent Leave Requests -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Quick Actions</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-wrap gap-2">
                    <Button variant="outline" href="/employees/create" as={Link}>
                        Add Employee
                    </Button>
                    <Button variant="outline" href="/leave/create" as={Link}>
                        Request Leave
                    </Button>
                    <Button variant="outline" href="/payroll" as={Link}>
                        Manage Payroll
                    </Button>
                </CardContent>
            </Card>

            <!-- Recent Leave Requests -->
            <Card>
                <CardHeader>
                    <CardTitle>Recent Leave Requests</CardTitle>
                </CardHeader>
                <CardContent>
                    {#if recentLeaveRequests.length > 0}
                        <div class="space-y-3">
                            {#each recentLeaveRequests as request}
                                <div class="flex items-center justify-between rounded-lg border p-3">
                                    <div>
                                        <p class="font-medium">{request.employee.name}</p>
                                        <p class="text-sm text-muted-foreground">
                                            {request.days_count} day(s) - {request.reason}
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Pending
                                    </span>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <p class="text-center text-muted-foreground py-4">No pending leave requests</p>
                    {/if}
                </CardContent>
            </Card>
        </div>
    </div>
</AppLayout>
