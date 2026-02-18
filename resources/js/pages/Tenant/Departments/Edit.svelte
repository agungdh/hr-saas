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
        department,
    }: {
        department: {
            id: number;
            name: string;
            description: string | null;
        };
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Departments', href: '/departments' },
        { title: 'Edit', href: `/departments/${department.id}/edit` },
    ];

    const form = useForm({
        name: department.name,
        description: department.description || '',
    });

    function submit(): void {
        form.put(`/departments/${department.id}`);
    }
</script>

<AppHead title="Edit Department" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Edit Department</h1>
            <p class="text-sm text-muted-foreground">Update department information</p>
        </div>

        <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="max-w-xl space-y-6">
            <div class="space-y-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    type="text"
                    bind:value={form.fields.name}
                    error={form.errors.name}
                    placeholder="Engineering"
                    required
                />
                {#if form.errors.name}
                    <p class="text-sm text-red-600">{form.errors.name}</p>
                {/if}
            </div>

            <div class="space-y-2">
                <Label for="description">Description</Label>
                <textarea
                    id="description"
                    bind:value={form.fields.description}
                    rows={3}
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Department description..."
                />
                {#if form.errors.description}
                    <p class="text-sm text-red-600">{form.errors.description}</p>
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
                        <Link {...props} href="/departments">Cancel</Link>
                    {/snippet}
                </Button>
            </div>
        </form>
    </div>
</AppLayout>
