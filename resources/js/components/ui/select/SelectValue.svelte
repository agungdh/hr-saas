<script lang="ts">
    import type { Snippet } from 'svelte';
    import { getContext } from 'svelte';
    import { cn } from '@/lib/utils';
    import { SELECT_CONTEXT, type SelectContext } from './context';

    let {
        placeholder = '',
        class: className = '',
        children,
    }: {
        placeholder?: string;
        class?: string;
        children?: Snippet;
    } = $props();

    const { value } = getContext<SelectContext>(SELECT_CONTEXT);
</script>

<span class={cn('block truncate', className)}>
    {#if value()}
        {#if children}
            {@render children()}
        {:else}
            {value()}
        {/if}
    {:else}
        <span class="text-muted-foreground">{placeholder}</span>
    {/if}
</span>
