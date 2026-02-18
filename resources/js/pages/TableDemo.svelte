<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { DataTableServer } from '@/components/ui/table';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';

    interface Employee {
        id: number;
        name: string;
        email: string;
        department: string;
        position: string;
        status: 'active' | 'inactive' | 'on-leave';
        salary: number;
        start_date: string;
    }

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Table Demo',
            href: '/table-demo',
        },
    ];

    // Column definitions
    const columns = [
        { id: 'name', header: 'Name', accessorKey: 'name' as const },
        { id: 'email', header: 'Email', accessorKey: 'email' as const },
        { id: 'department', header: 'Department', accessorKey: 'department' as const },
        { id: 'position', header: 'Position', accessorKey: 'position' as const },
        {
            id: 'status',
            header: 'Status',
            accessorKey: 'status' as const,
            cell: (row: Employee) => {
                const labels: Record<string, string> = {
                    active: 'Active',
                    inactive: 'Inactive',
                    'on-leave': 'On Leave',
                };
                return labels[row.status] ?? row.status;
            },
        },
        {
            id: 'salary',
            header: 'Salary',
            accessorKey: 'salary' as const,
            cell: (row: Employee) => {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(row.salary);
            },
        },
        { id: 'start_date', header: 'Start Date', accessorKey: 'start_date' as const },
    ];
</script>

<AppHead title="Table Demo - Server-Side DataTable" />

<AppLayout {breadcrumbs}>
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Employee Directory</h1>
                <p class="text-muted-foreground">
                    Server-side DataTable with search, sort, and pagination - optimized for 1B+ records
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
            <DataTableServer
                {columns}
                endpoint="/api/employees"
                pageSize={10}
                searchPlaceholder="Search by name, email, department, position..."
            />
        </div>

        <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
            <h2 class="mb-4 text-lg font-semibold">Features</h2>
            <ul class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Server-side search
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Server-side sorting
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Server-side pagination
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Database indexes
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Debounced search
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Loading states
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Error handling
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> 1B+ records ready
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Svelte 5 runes
                </li>
            </ul>
        </div>
    </div>
</AppLayout>
