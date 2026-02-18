<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Link } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import { MoreHorizontal } from 'lucide-svelte';

    let {
        tenants,
    }: {
        tenants: Array<{
            id: string;
            name: string;
            slug: string;
            plan: string;
            trial_ends_at: string | null;
            subscription_ends_at: string | null;
            created_at: string;
            domains: Array<{ domain: string }>;
        }>;
    } = $props();

    const breadcrumbItems: BreadcrumbItem[] = [
        {
            title: 'Tenants',
            href: '/admin/tenants',
        },
    ];

    const flash = $derived($page.props.flash as { success?: string; error?: string } | undefined);
</script>

<AppHead title="Tenants" />

<AppLayout breadcrumbs={breadcrumbItems}>
    <div class="space-y-6 px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Tenants</h1>
                <p class="text-sm text-muted-foreground">Manage your SaaS tenants</p>
            </div>
            <Button asChild>
                {#snippet children(props)}
                    <Link {...props} href="/admin/tenants/create">Create Tenant</Link>
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
                            <th class="px-4 py-3 text-left font-medium">Domain</th>
                            <th class="px-4 py-3 text-left font-medium">Plan</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-left font-medium">Created</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each tenants as tenant}
                            <tr class="border-b border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{tenant.name}</div>
                                    <div class="text-xs text-muted-foreground">{tenant.id}</div>
                                </td>
                                <td class="px-4 py-3">
                                    {#each tenant.domains as domain}
                                        <div class="text-muted-foreground">{domain.domain}</div>
                                    {/each}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                        {tenant.plan === 'enterprise' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
                                          tenant.plan === 'pro' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                          'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'}">
                                        {tenant.plan}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {#if tenant.subscription_ends_at}
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    {:else if tenant.trial_ends_at}
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Trial
                                        </span>
                                    {:else}
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                            Free
                                        </span>
                                    {/if}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {new Date(tenant.created_at).toLocaleDateString()}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild let:builder>
                                            <Button
                                                builders={[builder]}
                                                variant="ghost"
                                                class="h-8 w-8 p-0"
                                            >
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem>
                                                <Link href="/admin/tenants/{tenant.id}">View</Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem>
                                                <Link href="/admin/tenants/{tenant.id}/edit">Edit</Link>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        {/each}
                        {#if tenants.length === 0}
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                    No tenants found. Create your first tenant to get started.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AppLayout>
