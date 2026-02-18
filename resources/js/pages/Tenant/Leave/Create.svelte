<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { useForm } from '@inertiajs/svelte';
    import { Link } from '@inertiajs/svelte';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

    let {
        employees,
    }: {
        employees: Array<{
            id: number;
            name: string;
            department: { name: string } | null;
        }>;
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Leave Management', href: '/leave' },
        { title: 'New Request', href: '/leave/create' },
    ];

    const form = useForm({
        employee_id: '',
        start_date: '',
        end_date: '',
        reason: '',
    });

    let calculatedDays = $derived.by(() => {
        if (!form.fields.start_date || !form.fields.end_date) return 0;

        const start = new Date(form.fields.start_date);
        const end = new Date(form.fields.end_date);
        let count = 0;

        const current = new Date(start);
        while (current <= end) {
            const day = current.getDay();
            if (day !== 0 && day !== 6) { // Not weekend
                count++;
            }
            current.setDate(current.getDate() + 1);
        }

        return count;
    });

    function submit(): void {
        form.post('/leave');
    }
</script>

<AppHead title="Request Leave" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Request Leave</h1>
            <p class="text-sm text-muted-foreground">Submit a leave request for an employee</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-6 lg:col-span-2">
                <div class="space-y-2">
                    <Label for="employee_id">Employee</Label>
                    <select
                        id="employee_id"
                        bind:value={form.fields.employee_id}
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        required
                    >
                        <option value="">Select an employee</option>
                        {#each employees as employee}
                            <option value={employee.id}>{employee.name} ({employee.department?.name || 'No Department'})</option>
                        {/each}
                    </select>
                    {#if form.errors.employee_id}
                        <p class="text-sm text-red-600">{form.errors.employee_id}</p>
                    {/if}
                </div>

                <div class="grid gap-4 md:grid-cols-2">
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

                    <div class="space-y-2">
                        <Label for="end_date">End Date</Label>
                        <Input
                            id="end_date"
                            type="date"
                            bind:value={form.fields.end_date}
                            required
                        />
                        {#if form.errors.end_date}
                            <p class="text-sm text-red-600">{form.errors.end_date}</p>
                        {/if}
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="reason">Reason</Label>
                    <textarea
                        id="reason"
                        bind:value={form.fields.reason}
                        rows={3}
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        placeholder="Please provide a reason for the leave request..."
                        required
                    />
                    {#if form.errors.reason}
                        <p class="text-sm text-red-600">{form.errors.reason}</p>
                    {/if}
                </div>

                <div class="flex gap-3">
                    <Button type="submit" disabled={form.processing}>
                        {#if form.processing}
                            Submitting...
                        {:else}
                            Submit Request
                        {/if}
                    </Button>
                    <Button variant="outline" asChild>
                    {#snippet children(props)}
                        <Link {...props} href="/leave">Cancel</Link>
                    {/snippet}
                </Button>
                </div>
            </form>

            <!-- Summary Card -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Leave Summary</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <p class="text-sm text-muted-foreground">Working Days</p>
                        <p class="text-2xl font-bold">{calculatedDays}</p>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        This count excludes weekends (Saturday and Sunday).
                        The request will be subject to the employee's available leave quota.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</AppLayout>
