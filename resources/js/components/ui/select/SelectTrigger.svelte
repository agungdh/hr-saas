<script lang="ts">
    import type { Snippet } from 'svelte';
    import { getContext } from 'svelte';
    import { cn } from '@/lib/utils';
    import { SELECT_CONTEXT, type SelectContext } from './context';

    type TriggerProps = {
        class?: string;
        type?: string;
        onclick?: (event: MouseEvent) => void;
        'aria-expanded'?: boolean;
        'aria-disabled'?: boolean;
        'data-state'?: 'open' | 'closed';
        [key: string]: any;
    };

    let {
        asChild = false,
        disabled = false,
        class: className = '',
        children,
    }: {
        asChild?: boolean;
        disabled?: boolean;
        class?: string;
        children?: Snippet<[TriggerProps]>;
    } = $props();

    const { open, setOpen } = getContext<SelectContext>(SELECT_CONTEXT);

    const handleClick = () => {
        if (!disabled) {
            setOpen(!open());
        }
    };

    const triggerProps = $derived<TriggerProps>({
        class: cn(
            'flex h-9 w-full items-center justify-between whitespace-nowrap rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1',
            className,
        ),
        type: 'button',
        onclick: handleClick,
        'aria-expanded': open(),
        'aria-disabled': disabled,
        'data-state': open() ? 'open' : 'closed',
    });
</script>

{#if asChild}
    {@render children?.(triggerProps)}
{:else}
    <button {...triggerProps}>
        {@render children?.({})}
    </button>
{/if}
