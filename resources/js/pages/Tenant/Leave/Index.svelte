<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Link, router } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogTitle,
    } from '@/components/ui/dialog';
    import { Textarea } from '@/components/ui/textarea';
    import { Check, X } from 'lucide-svelte';

    let {
        leaveRequests,
        filters,
    }: {
        leaveRequests: {
            data: Array<{
                id: number;
                start_date: string;
                end_date: string;
                days_count: number;
                reason: string;
                status: string;
                notes: string | null;
                employee: {
                    id: number;
                    name: string;
                    department: { name: string } | null;
                };
            }>;
            links: Array<{ url: string | null; label: string; active: boolean }>;
        };
        filters: { status: string };
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Leave Management', href: '/leave' },
    ];

    const flash = $derived($page.props.flash as { success?: string; error?: string } | undefined);

    let selectedStatus = $state(filters.status);
    let rejectDialogOpen = $state(false);
    let selectedRequest = $state<number | null>(null);
    let rejectNotes = $state('');

    function filterStatus(): void {
        router.get('/leave', { status: selectedStatus }, { preserveState: true });
    }

    function openRejectDialog(id: number): void {
        selectedRequest = id;
        rejectNotes = '';
        rejectDialogOpen = true;
    }

    function approveRequest(id: number): void {
        router.post(`/leave/${id}/approve`);
    }

    function rejectRequest(): void {
        if (selectedRequest) {
            router.post(`/leave/${selectedRequest}/reject`, { notes: rejectNotes });
            rejectDialogOpen = false;
        }
    }

    function getStatusClass(status: string): string {
        switch (status) {
            case 'approved': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            case 'rejected': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
            default: return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        }
    }
</script>

<AppHead title="Leave Management" />

<AppLayout {breadcrumbs}>
    <div class="space-y-6 px-4 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Leave Management</h1>
                <p class="text-sm text-muted-foreground">Manage employee leave requests</p>
            </div>
            <Button href="/leave/create" as={Link}>Request Leave</Button>
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

        <!-- Filter -->
        <div class="flex items-center gap-4">
            <select
                bind:value={selectedStatus}
                onchange={filterStatus}
                class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
            >
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <!-- Leave Requests Table -->
        <div class="rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Employee</th>
                            <th class="px-4 py-3 text-left font-medium">Duration</th>
                            <th class="px-4 py-3 text-left font-medium">Reason</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each leaveRequests.data as request}
                            <tr class="border-b border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{request.employee.name}</div>
                                    <div class="text-xs text-muted-foreground">{request.employee.department?.name || 'No Department'}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{request.days_count} day(s)</div>
                                    <div class="text-xs text-muted-foreground">
                                        {new Date(request.start_date).toLocaleDateString()} - {new Date(request.end_date).toLocaleDateString()}
                                    </div>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate">{request.reason}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium capitalize {getStatusClass(request.status)}">
                                        {request.status}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {#if request.status === 'pending'}
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                class="text-green-600 hover:text-green-700"
                                                onclick={() => approveRequest(request.id)}
                                            >
                                                <Check class="h-4 w-4" />
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                class="text-red-600 hover:text-red-700"
                                                onclick={() => openRejectDialog(request.id)}
                                            >
                                                <X class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    {:else if request.notes}
                                        <span class="text-xs text-muted-foreground">{request.notes}</span>
                                    {/if}
                                </td>
                            </tr>
                        {/each}
                        {#if leaveRequests.data.length === 0}
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                                    No leave requests found.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</AppLayout>

<!-- Reject Dialog -->
<Dialog bind:open={rejectDialogOpen}>
    <DialogContent>
        <DialogHeader>
            <DialogTitle>Reject Leave Request</DialogTitle>
            <DialogDescription>
                Please provide a reason for rejecting this leave request.
            </DialogDescription>
        </DialogHeader>
        <div class="py-4">
            <textarea
                bind:value={rejectNotes}
                rows={3}
                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                placeholder="Optional notes..."
            />
        </div>
        <DialogFooter>
            <Button variant="outline" onclick={() => rejectDialogOpen = false}>Cancel</Button>
            <Button variant="destructive" onclick={rejectRequest}>Reject</Button>
        </DialogFooter>
    </DialogContent>
</Dialog>
