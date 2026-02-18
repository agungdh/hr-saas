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
    import { MoreHorizontal, Pencil, Trash2 } from 'lucide-svelte';

    let {
        departments,
    }: {
        departments: {
            data: Array<{
                id: number;
                name: string;
                description: string | null;
                employees_count: number;
            }>;
            links: Array<{ url: string | null; label: string; active: boolean }>;
        };
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Departments', href: '/departments' },
    ];

    const flash = $derived($page.props.flash as { success?: string; error?: string } | undefined);

    function deleteDepartment(id: number): void {
        if (confirm('Are you sure you want to delete this department?')) {
            router.delete(`/departments/${id}`);
        }
    }
</script>

<AppHead title="Departments" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Departments</h1>
                <p class="text-sm text-muted-foreground">Manage your departments</p>
            </div>
            <Button asChild>
                {#snippet children(props)}
                    <Link {...props} href="/departments/create">Create Department</Link>
                {/snippet}
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

        <div class="rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Name</th>
                            <th class="px-4 py-3 text-left font-medium">Description</th>
                            <th class="px-4 py-3 text-left font-medium">Employees</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each departments.data as department}
                            <tr class="border-b border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50">
                                <td class="px-4 py-3 font-medium">{department.name}</td>
                                <td class="px-4 py-3 text-muted-foreground">{department.description || '-'}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {department.employees_count}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild let:builder>
                                            <Button builders={[builder]} variant="ghost" class="h-8 w-8 p-0">
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem>
                                                <Link href="/departments/{department.id}/edit" class="flex items-center gap-2">
                                                    <Pencil class="h-4 w-4" />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="text-red-600" onclick={() => deleteDepartment(department.id)}>
                                                <Trash2 class="h-4 w-4" />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        {/each}
                        {#if departments.data.length === 0}
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                                    No departments found. Create your first department to get started.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AppLayout>
