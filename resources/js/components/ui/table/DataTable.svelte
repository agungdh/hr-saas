<script lang="ts" generics="T extends Record<string, any>">
    import { cn } from '@/lib/utils';
    import * as Table from './index';
    import { Button } from '@/components/ui/button';
    import {
        ChevronFirst,
        ChevronLast,
        ChevronLeft,
        ChevronRight,
        ArrowUpDown,
    } from 'lucide-svelte';

    type ColumnDef<T> = {
        id: string;
        header: string;
        accessorKey: keyof T | ((row: T) => unknown);
        cell?: (row: T) => unknown;
    };

    interface Props<T> {
        columns: ColumnDef<T>[];
        data: T[];
        class?: string;
        pageSize?: number;
        enableSorting?: boolean;
        enablePagination?: boolean;
    }

    let {
        columns,
        data,
        class: className = '',
        pageSize = 10,
        enableSorting = true,
        enablePagination = true,
    }: Props<T> = $props();

    type SortDirection = 'asc' | 'desc' | null;
    let sortColumn = $state<string | null>(null);
    let sortDirection = $state<SortDirection>(null);
    let currentPage = $state(0);

    // Sorting logic
    const sortedData = $derived.by(() => {
        if (!sortColumn || !sortDirection) {
            return data;
        }

        const column = columns.find((col) => col.id === sortColumn);
        if (!column) return data;

        return [...data].sort((a, b) => {
            let aVal: unknown;
            let bVal: unknown;

            if (typeof column.accessorKey === 'function') {
                aVal = column.accessorKey(a);
                bVal = column.accessorKey(b);
            } else {
                aVal = a[column.accessorKey];
                bVal = b[column.accessorKey];
            }

            if (aVal === undefined || aVal === null) return 1;
            if (bVal === undefined || bVal === null) return -1;

            if (typeof aVal === 'string' && typeof bVal === 'string') {
                return sortDirection === 'asc'
                    ? aVal.localeCompare(bVal)
                    : bVal.localeCompare(aVal);
            }

            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return sortDirection === 'asc' ? aVal - bVal : bVal - aVal;
            }

            return 0;
        });
    });

    // Pagination
    const totalPages = $derived.by(() => Math.ceil(sortedData.length / pageSize));
    const paginatedData = $derived.by(() => {
        const start = currentPage * pageSize;
        return sortedData.slice(start, start + pageSize);
    });

    const canPreviousPage = $derived.by(() => currentPage > 0);
    const canNextPage = $derived.by(() => currentPage < totalPages - 1);

    function toggleSort(columnId: string) {
        if (!enableSorting) return;

        if (sortColumn === columnId) {
            if (sortDirection === 'asc') {
                sortDirection = 'desc';
            } else if (sortDirection === 'desc') {
                sortColumn = null;
                sortDirection = null;
            } else {
                sortDirection = 'asc';
            }
        } else {
            sortColumn = columnId;
            sortDirection = 'asc';
        }
        currentPage = 0; // Reset to first page when sorting
    }

    function getCellValue(row: T, column: ColumnDef<T>): unknown {
        if (column.cell) {
            return column.cell(row);
        }
        if (typeof column.accessorKey === 'function') {
            return column.accessorKey(row);
        }
        return row[column.accessorKey];
    }
</script>

<div class={cn('w-full', className)}>
    <!-- Table -->
    <div class="rounded-md border">
        <Table.Table>
            <Table.TableHeader>
                <Table.TableRow>
                    {#each columns as column}
                        <Table.TableHead
                            class={cn(
                                enableSorting &&
                                    'cursor-pointer select-none hover:bg-muted/50',
                            )}
                            onclick={() => toggleSort(column.id)}
                        >
                            <div class="flex items-center gap-2">
                                {column.header}
                                {#if enableSorting}
                                    <ArrowUpDown
                                        class={cn(
                                            'h-4 w-4 opacity-20',
                                            sortColumn === column.id && 'opacity-100',
                                            sortColumn === column.id &&
                                                sortDirection === 'asc' &&
                                                'rotate-0',
                                            sortColumn === column.id &&
                                                sortDirection === 'desc' &&
                                                'rotate-180',
                                        )}
                                    />
                                {/if}
                            </div>
                        </Table.TableHead>
                    {/each}
                </Table.TableRow>
            </Table.TableHeader>
            <Table.TableBody>
                {#if paginatedData.length > 0}
                    {#each paginatedData as row, index (index + '-' + currentPage)}
                        <Table.TableRow>
                            {#each columns as column}
                                <Table.TableCell>
                                    {getCellValue(row, column)}
                                </Table.TableCell>
                            {/each}
                        </Table.TableRow>
                    {/each}
                {:else}
                    <Table.TableRow>
                        <Table.TableCell
                            colspan={columns.length}
                            class="h-24 text-center"
                        >
                            No results.
                        </Table.TableCell>
                    </Table.TableRow>
                {/if}
            </Table.TableBody>
        </Table.Table>
    </div>

    <!-- Pagination -->
    {#if enablePagination && totalPages > 1}
        <div class="flex items-center justify-end space-x-2 py-4">
            <div class="flex-1 text-sm text-muted-foreground">
                Page {currentPage + 1} of {totalPages}
            </div>
            <div class="space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => (currentPage = 0)}
                    disabled={!canPreviousPage}
                >
                    <ChevronFirst class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => currentPage--}
                    disabled={!canPreviousPage}
                >
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => currentPage++}
                    disabled={!canNextPage}
                >
                    <ChevronRight class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => currentPage = totalPages - 1}
                    disabled={!canNextPage}
                >
                    <ChevronLast class="h-4 w-4" />
                </Button>
            </div>
        </div>
    {/if}
</div>

<style>
    :global(.rotate-0) {
        transform: rotate(0deg);
    }
    :global(.rotate-180) {
        transform: rotate(180deg);
    }
</style>
