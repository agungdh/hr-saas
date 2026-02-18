<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Link, router } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import { MoreHorizontal, Pencil, Trash2, AlertTriangle } from 'lucide-svelte';

    let {
        employees,
        limitInfo,
    }: {
        employees: {
            data: Array<{
                id: number;
                name: string;
                email: string;
                position: string;
                status: string;
                base_salary: number;
                start_date: string;
                department: { id: number; name: string } | null;
            }>;
            links: Array<{ url: string | null; label: string; active: boolean }>;
        };
        limitInfo: {
            plan: string;
            current_count: number;
            limit: number | string;
            remaining: number | string;
            is_unlimited: boolean;
            can_add_more: boolean;
        };
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Employees', href: '/employees' },
    ];

    const flash = $derived($page.props.flash as { success?: string; error?: string } | undefined);

    function deleteEmployee(id: number): void {
        if (confirm('Are you sure you want to delete this employee?')) {
            router.delete(`/employees/${id}`);
        }
    }

    function formatCurrency(amount: number): string {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
    }

    function getStatusClass(status: string): string {
        switch (status) {
            case 'active': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            case 'inactive': return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
            case 'on-leave': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
            default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
        }
    }
</script>

<AppHead title="Employees" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Employees</h1>
                <p class="text-sm text-muted-foreground">
                    {limitInfo.current_count} {#if limitInfo.is_unlimited}employees{:else}of {limitInfo.limit}{/if} employees
                </p>
            </div>
            <Button href="/employees/create" as={Link} disabled={!limitInfo.can_add_more}>
                Add Employee
            </Button>
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

        {#if !limitInfo.is_unlimited && !limitInfo.can_add_more}
            <div class="flex items-center gap-3 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-950">
                <AlertTriangle class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                <div>
                    <p class="font-medium text-yellow-800 dark:text-yellow-200">Employee Limit Reached</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">Upgrade to Pro for unlimited employees.</p>
                </div>
            </div>
        {/if}

        <div class="rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Department</th>
                            <th class="px-4 py-3 text-left font-medium">Position</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-left font-medium">Base Salary</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each employees.data as employee}
                            <tr class="border-b border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{employee.name}</div>
                                    <div class="text-xs text-muted-foreground">{employee.email}</div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {employee.department?.name || '-'}
                                </td>
                                <td class="px-4 py-3">{employee.position}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium capitalize {getStatusClass(employee.status)}">
                                        {employee.status.replace('-', ' ')}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{formatCurrency(employee.base_salary)}</td>
                                <td class="px-4 py-3 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild let:builder>
                                            <Button builders={[builder]} variant="ghost" class="h-8 w-8 p-0">
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem>
                                                <Link href="/employees/{employee.id}/edit" class="flex items-center gap-2">
                                                    <Pencil class="h-4 w-4" />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="text-red-600" onclick={() => deleteEmployee(employee.id)}>
                                                <Trash2 class="h-4 w-4" />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        {/each}
                        {#if employees.data.length === 0}
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                    No employees found. Add your first employee to get started.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AppLayout>
