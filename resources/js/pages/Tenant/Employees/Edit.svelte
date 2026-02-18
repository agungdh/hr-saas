<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { useForm } from '@inertiajs/svelte';
    import { Link } from '@inertiajs/svelte';

    let {
        employee,
        departments,
    }: {
        employee: {
            id: number;
            department_id: number | null;
            name: string;
            email: string;
            position: string;
            status: string;
            base_salary: number;
            start_date: string;
        };
        departments: Array<{ id: number; name: string }>;
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Employees', href: '/employees' },
        { title: 'Edit', href: `/employees/${employee.id}/edit` },
    ];

    const form = useForm({
        department_id: employee.department_id?.toString() || '',
        name: employee.name,
        email: employee.email,
        position: employee.position,
        status: employee.status,
        base_salary: employee.base_salary.toString(),
        start_date: employee.start_date,
    });

    function submit(): void {
        form.put(`/employees/${employee.id}`);
    }
</script>

<AppHead title="Edit Employee" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Edit Employee</h1>
            <p class="text-sm text-muted-foreground">Update employee information</p>
        </div>

        <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="max-w-xl space-y-6">
            <div class="space-y-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    type="text"
                    bind:value={form.fields.name}
                    placeholder="John Doe"
                    required
                />
                {#if form.errors.name}
                    <p class="text-sm text-red-600">{form.errors.name}</p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    bind:value={form.fields.email}
                    placeholder="john@example.com"
                    required
                />
                {#if form.errors.email}
                    <p class="text-sm text-red-600">{form.errors.email}</p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="department_id">Department</Label>
                <select
                    id="department_id"
                    bind:value={form.fields.department_id}
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    <option value="">Select a department</option>
                    {#each departments as department}
                        <option value={department.id}>{department.name}</option>
                    {/each}
                </select>
                {#if form.errors.department_id}
                    <p class="text-sm text-red-600">{form.errors.department_id}</p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="position">Position</Label>
                <Input
                    id="position"
                    type="text"
                    bind:value={form.fields.position}
                    placeholder="Software Engineer"
                    required
                />
                {#if form.errors.position}
                    <p class="text-sm text-red-600">{form.errors.position}</p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="status">Status</Label>
                <select
                    id="status"
                    bind:value={form.fields.status}
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="on-leave">On Leave</option>
                </select>
                {#if form.errors.status}
                    <p class="text-sm text-red-600">{form.errors.status}</p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="base_salary">Base Salary (IDR)</Label>
                <Input
                    id="base_salary"
                    type="number"
                    bind:value={form.fields.base_salary}
                    placeholder="10000000"
                    required
                />
                {#if form.errors.base_salary}
                    <p class="text-sm text-red-600">{form.errors.base_salary}</p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="start_date">Start Date</Label>
                <Input
                    id="start_date"
                    type="date"
                    bind:value={form.fields.start_date}
                    required
                />
                {#if form.errors.start_date}
                    <p class="text-sm text-red-600">{form.errors.start_date}</p>
                {/if}
            </div>

            <div class="flex gap-3">
                <Button type="submit" disabled={form.processing}>
                    {#if form.processing}
                        Saving...
                    {:else}
                        Save Changes
                    {/if}
                </Button>
                <Button variant="outline" asChild>
                    {#snippet children(props)}
                        <Link {...props} href="/employees">Cancel</Link>
                    {/snippet}
                </Button>
            </div>
        </form>
    </div>
</AppLayout>
