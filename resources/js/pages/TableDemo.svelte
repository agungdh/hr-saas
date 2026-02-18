<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { DataTable } from '@/components/ui/table';

    interface Employee {
        id: number;
        name: string;
        email: string;
        department: string;
        position: string;
        status: 'active' | 'inactive' | 'on-leave';
        salary: number;
        startDate: string;
    }

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Table Demo',
            href: '/table-demo',
        },
    ];

    // Sample data
    const employees: Employee[] = [
        {
            id: 1,
            name: 'John Doe',
            email: 'john.doe@example.com',
            department: 'Engineering',
            position: 'Senior Developer',
            status: 'active',
            salary: 95000,
            startDate: '2021-03-15',
        },
        {
            id: 2,
            name: 'Jane Smith',
            email: 'jane.smith@example.com',
            department: 'Marketing',
            position: 'Marketing Manager',
            status: 'active',
            salary: 85000,
            startDate: '2020-06-01',
        },
        {
            id: 3,
            name: 'Bob Johnson',
            email: 'bob.johnson@example.com',
            department: 'Engineering',
            position: 'Junior Developer',
            status: 'inactive',
            salary: 65000,
            startDate: '2023-01-10',
        },
        {
            id: 4,
            name: 'Alice Williams',
            email: 'alice.williams@example.com',
            department: 'HR',
            position: 'HR Specialist',
            status: 'active',
            salary: 70000,
            startDate: '2019-09-20',
        },
        {
            id: 5,
            name: 'Charlie Brown',
            email: 'charlie.brown@example.com',
            department: 'Finance',
            position: 'Financial Analyst',
            status: 'on-leave',
            salary: 80000,
            startDate: '2021-11-05',
        },
        {
            id: 6,
            name: 'Diana Prince',
            email: 'diana.prince@example.com',
            department: 'Engineering',
            position: 'Tech Lead',
            status: 'active',
            salary: 120000,
            startDate: '2018-04-12',
        },
        {
            id: 7,
            name: 'Eve Davis',
            email: 'eve.davis@example.com',
            department: 'Sales',
            position: 'Sales Representative',
            status: 'active',
            salary: 75000,
            startDate: '2022-02-28',
        },
        {
            id: 8,
            name: 'Frank Miller',
            email: 'frank.miller@example.com',
            department: 'Operations',
            position: 'Operations Manager',
            status: 'active',
            salary: 90000,
            startDate: '2019-07-15',
        },
        {
            id: 9,
            name: 'Grace Lee',
            email: 'grace.lee@example.com',
            department: 'Design',
            position: 'UI/UX Designer',
            status: 'active',
            salary: 82000,
            startDate: '2021-08-30',
        },
        {
            id: 10,
            name: 'Henry Wilson',
            email: 'henry.wilson@example.com',
            department: 'Engineering',
            position: 'DevOps Engineer',
            status: 'inactive',
            salary: 98000,
            startDate: '2020-05-18',
        },
        {
            id: 11,
            name: 'Ivy Chen',
            email: 'ivy.chen@example.com',
            department: 'Marketing',
            position: 'Content Strategist',
            status: 'active',
            salary: 78000,
            startDate: '2022-09-01',
        },
        {
            id: 12,
            name: 'Jack Taylor',
            email: 'jack.taylor@example.com',
            department: 'HR',
            position: 'Recruiter',
            status: 'on-leave',
            salary: 68000,
            startDate: '2023-03-22',
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
        { id: 'startDate', header: 'Start Date', accessorKey: 'startDate' as const },
    ];
</script>

<AppHead title="Table Demo - DataTable" />

<AppLayout {breadcrumbs}>
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Employee Directory</h1>
                <p class="text-muted-foreground">
                    A custom DataTable component with Svelte 5 runes
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
            <DataTable
                {columns}
                data={employees}
                pageSize={5}
            />
        </div>

        <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
            <h2 class="mb-4 text-lg font-semibold">Features</h2>
            <ul class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Column sorting
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Pagination
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> TypeScript generics
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Tailwind CSS styling
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Custom cell renderers
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-primary">✓</span> Svelte 5 runes
                </li>
            </ul>
        </div>
    </div>
</AppLayout>
