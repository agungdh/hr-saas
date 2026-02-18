import type {
    Column,
    ColumnDef,
    ColumnFiltersState,
    PaginationState,
    RowSelectionState,
    SortingState,
    TableOptions,
    VisibilityState,
} from '@tanstack/table-core';

export type { Column, ColumnDef };

export type FilterOption = {
    label: string;
    value: string;
};

export type ColumnFilter = {
    id: string;
    value: string | string[] | number | number[];
};

export type TableColumnDef<T> = ColumnDef<T, unknown> & {
    filterable?: boolean;
    filterOptions?: FilterOption[];
};

export type TableState = {
    sorting: SortingState;
    columnFilters: ColumnFiltersState;
    columnVisibility: VisibilityState;
    rowSelection: RowSelectionState;
    pagination: PaginationState;
};

export type TableConfig<T> = {
    columns: TableColumnDef<T>[];
    data: T[];
    initialState?: Partial<TableState>;
    enableRowSelection?: boolean;
    enableMultiRowSelection?: boolean;
    enableColumnFilters?: boolean;
    enableSorting?: boolean;
    enablePagination?: boolean;
    pageSize?: number;
};

// flexRender helper function for TanStack Table
export function flexRender<T>(
    template: string | ((props: T) => unknown) | unknown,
    props: T,
): unknown {
    if (typeof template === 'function') {
        return template(props);
    }
    return template;
}

export function valueToLabel(value: string, options: FilterOption[]): string {
    return options.find((opt) => opt.value === value)?.label || value;
}

export function filterRows<T>(
    rows: T[],
    filters: ColumnFiltersState,
): T[] {
    let filtered = rows;

    for (const filter of filters) {
        const { id, value } = filter;
        if (value === undefined || value === null || value === '') continue;

        filtered = filtered.filter((row) => {
            const rowValue = (row as Record<string, unknown>)[id];
            if (typeof rowValue === 'string') {
                return rowValue.toLowerCase().includes(String(value).toLowerCase());
            }
            if (typeof rowValue === 'number') {
                return rowValue === Number(value);
            }
            return String(rowValue).toLowerCase().includes(String(value).toLowerCase());
        });
    }

    return filtered;
}

export function sortRows<T>(rows: T[], sorting: SortingState): T[] {
    if (!sorting.length) return rows;

    const { id, desc } = sorting[0];

    return [...rows].sort((a, b) => {
        const aValue = (a as Record<string, unknown>)[id];
        const bValue = (b as Record<string, unknown>)[id];

        if (aValue === undefined && bValue === undefined) return 0;
        if (aValue === undefined) return desc ? -1 : 1;
        if (bValue === undefined) return desc ? 1 : -1;

        if (typeof aValue === 'string' && typeof bValue === 'string') {
            return desc
                ? bValue.localeCompare(aValue)
                : aValue.localeCompare(bValue);
        }

        if (typeof aValue === 'number' && typeof bValue === 'number') {
            return desc ? bValue - aValue : aValue - bValue;
        }

        return 0;
    });
}

export function paginateRows<T>(
    rows: T[],
    pagination: PaginationState,
): T[] {
    const start = pagination.pageIndex * pagination.pageSize;
    const end = start + pagination.pageSize;
    return rows.slice(start, end);
}

export function createTableState<T>(
    config: TableConfig<T>,
): TableState {
    return {
        sorting: config.initialState?.sorting ?? [],
        columnFilters: config.initialState?.columnFilters ?? [],
        columnVisibility: config.initialState?.columnVisibility ?? {},
        rowSelection: config.initialState?.rowSelection ?? {},
        pagination: config.initialState?.pagination ?? {
            pageIndex: 0,
            pageSize: config.pageSize ?? 10,
        },
    };
}
