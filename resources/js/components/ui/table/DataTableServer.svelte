<script lang="ts" generics="T extends Record<string, any>">
    import { cn } from '@/lib/utils';
    import * as Table from './index';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import {
        ChevronFirst,
        ChevronLast,
        ChevronLeft,
        ChevronRight,
        ArrowUpDown,
        Search,
        Loader2,
    } from 'lucide-svelte';

    type ColumnDef<T> = {
        id: string;
        header: string;
        accessorKey: keyof T | string;
        cell?: (row: T) => unknown;
        sortable?: boolean;
    };

    interface DataTableResponse<T> {
        data: T[];
        meta: {
            current_page: number;
            from: number;
            last_page: number;
            per_page: number;
            to: number;
            total: number;
        };
    }

    interface Props<T> {
        columns: ColumnDef<T>[];
        endpoint: string;
        class?: string;
        pageSize?: number;
        enableSorting?: boolean;
        enablePagination?: boolean;
        enableSearch?: boolean;
        searchPlaceholder?: string;
        params?: Record<string, string | number | boolean>;
    }

    let {
        columns,
        endpoint,
        class: className = '',
        pageSize = 10,
        enableSorting = true,
        enablePagination = true,
        enableSearch = true,
        searchPlaceholder = 'Search...',
        params = {},
    }: Props<T> = $props();

    type SortDirection = 'asc' | 'desc' | null;
    let sortColumn = $state<string | null>(null);
    let sortDirection = $state<SortDirection>(null);
    let currentPage = $state(1);
    let searchQuery = $state('');
    let isLoading = $state(false);
    let data = $state<T[]>([]);
    let meta = $state<DataTableResponse<T>['meta'] | null>(null);
    let error = $state<string | null>(null);
    let searchTimeout: ReturnType<typeof setTimeout> | null = null;

    async function fetchData(): Promise<void> {
        isLoading = true;
        error = null;

        try {
            const url = new URL(window.location.origin + endpoint);
            url.searchParams.set('page', currentPage.toString());
            url.searchParams.set('per_page', pageSize.toString());

            if (searchQuery) {
                url.searchParams.set('search', searchQuery);
            }

            if (sortColumn) {
                url.searchParams.set('sort_by', sortColumn);
                url.searchParams.set('sort_direction', sortDirection || 'asc');
            }

            // Add custom params
            Object.entries(params).forEach(([key, value]) => {
                url.searchParams.set(key, String(value));
            });

            const response = await fetch(url.toString());
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result: DataTableResponse<T> = await response.json();
            data = result.data;
            meta = result.meta;
        } catch (e) {
            error = e instanceof Error ? e.message : 'Failed to fetch data';
            data = [];
            meta = null;
        } finally {
            isLoading = false;
        }
    }

    // Fetch data on mount
    $effect(() => {
        fetchData();
    });

    // Refetch when dependencies change
    $effect(() => {
        fetchData();
    }, [currentPage, sortColumn, sortDirection]);

    // Debounced search
    function handleSearchInput(value: string): void {
        searchQuery = value;
        currentPage = 1; // Reset to first page on search

        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        searchTimeout = setTimeout(() => {
            fetchData();
        }, 300);
    }

    function toggleSort(columnId: string): void {
        if (!enableSorting) return;

        const column = columns.find((col) => col.id === columnId);
        if (column?.sortable === false) return;

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
        currentPage = 1;
    }

    function getCellValue(row: T, column: ColumnDef<T>): unknown {
        if (column.cell) {
            return column.cell(row);
        }
        return row[column.accessorKey as keyof T];
    }

    const canPreviousPage = $derived.by(() => currentPage > 1);
    const canNextPage = $derived.by(() => meta ? currentPage < meta.last_page : false);
</script>

<div class={cn('w-full', className)}>
    <!-- Search -->
    {#if enableSearch}
        <div class="mb-4 flex items-center gap-2">
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground opacity-50" />
                <Input
                    type="text"
                    placeholder={searchPlaceholder}
                    bind:value={searchQuery}
                    oninput={(e) => handleSearchInput(e.currentTarget.value)}
                    class="pl-9"
                />
            </div>
            {#if isLoading}
                <Loader2 class="h-4 w-4 animate-spin text-muted-foreground" />
            {/if}
            {#if meta}
                <span class="text-sm text-muted-foreground">
                    {meta.total.toLocaleString()} records
                </span>
            {/if}
        </div>
    {/if}

    <!-- Table -->
    <div class="rounded-md border">
        <Table.Table>
            <Table.TableHeader>
                <Table.TableRow>
                    {#each columns as column}
                        <Table.TableHead
                            class={cn(
                                enableSorting &&
                                    column?.sortable !== false &&
                                    'cursor-pointer select-none hover:bg-muted/50',
                            )}
                            onclick={() => toggleSort(column.id)}
                        >
                            <div class="flex items-center gap-2">
                                {column.header}
                                {#if enableSorting && column?.sortable !== false}
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
                {#if isLoading && data.length === 0}
                    <Table.TableRow>
                        <Table.TableCell
                            colspan={columns.length}
                            class="h-24 text-center"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Loader2 class="h-4 w-4 animate-spin" />
                                Loading...
                            </div>
                        </Table.TableCell>
                    </Table.TableRow>
                {:else if error}
                    <Table.TableRow>
                        <Table.TableCell
                            colspan={columns.length}
                            class="h-24 text-center text-destructive"
                        >
                            {error}
                        </Table.TableCell>
                    </Table.TableRow>
                {:else if data.length > 0}
                    {#each data as row, index (row.id ?? index)}
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
                            No results found.
                        </Table.TableCell>
                    </Table.TableRow>
                {/if}
            </Table.TableBody>
        </Table.Table>
    </div>

    <!-- Pagination -->
    {#if enablePagination && meta && meta.last_page > 1}
        <div class="flex items-center justify-end space-x-2 py-4">
            <div class="flex-1 text-sm text-muted-foreground">
                Showing {meta.from ?? 0} to {meta.to ?? 0} of {meta.total.toLocaleString()} records
                (Page {meta.current_page} of {meta.last_page})
            </div>
            <div class="space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => (currentPage = 1)}
                    disabled={!canPreviousPage || isLoading}
                >
                    <ChevronFirst class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => currentPage--}
                    disabled={!canPreviousPage || isLoading}
                >
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => currentPage++}
                    disabled={!canNextPage || isLoading}
                >
                    <ChevronRight class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onclick={() => (currentPage = meta?.last_page ?? 1)}
                    disabled={!canNextPage || isLoading}
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
