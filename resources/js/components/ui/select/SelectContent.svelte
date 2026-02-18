<script lang="ts">
    import type { Snippet } from 'svelte';
    import { getContext } from 'svelte';
    import { cn } from '@/lib/utils';
    import { SELECT_CONTEXT, type SelectContext } from './context';

    let {
        position = 'popper',
        class: className = '',
        children,
    }: {
        position?: 'popper' | 'item-aligned';
        class?: string;
        children?: Snippet;
    } = $props();

    const { open, setOpen } = getContext<SelectContext>(SELECT_CONTEXT);

    const close = () => setOpen(false);
</script>

{#if open()}
    <div
        class={cn(
            'relative z-50 min-w-32 overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md',
            position === 'popper' &&
                'data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
            className,
        )}
        role="listbox"
        tabindex="-1"
        onkeydown={(event) => event.key === 'Escape' && close()}
    >
        {@render children?.()}
    </div>
{/if}
