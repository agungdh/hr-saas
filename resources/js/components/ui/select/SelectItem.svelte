<script lang="ts">
    import type { Snippet } from 'svelte';
    import { getContext } from 'svelte';
    import { cn } from '@/lib/utils';
    import { SELECT_CONTEXT, type SelectContext } from './context';

    type AsChildProps = {
        class?: string;
        onclick?: (event: MouseEvent) => void;
        'data-value'?: string;
        [key: string]: any;
    };

    let {
        value: itemValue = '',
        disabled = false,
        asChild = false,
        class: className = '',
        children,
    }: {
        value: string;
        disabled?: boolean;
        asChild?: boolean;
        class?: string;
        children?: Snippet<[AsChildProps]>;
    } = $props();

    const { value, setValue, setOpen } = getContext<SelectContext>(SELECT_CONTEXT);

    const isSelected = () => value() === itemValue;

    const handleClick = () => {
        if (!disabled) {
            setValue(itemValue);
            setOpen(false);
        }
    };

    const itemProps = $derived<AsChildProps>({
        class: cn(
            'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
            isSelected() && 'bg-accent text-accent-foreground',
            className,
        ),
        onclick: handleClick,
        'data-value': itemValue,
        'data-selected': isSelected(),
        'data-disabled': disabled,
    });
</script>

{#if asChild}
    {@render children?.(itemProps)}
{:else}
    <div
        role="option"
        tabindex="-1"
        aria-selected={isSelected()}
        data-value={itemValue}
        data-selected={isSelected()}
        onkeydown={(event) => (event.key === 'Enter' || event.key === ' ') && handleClick()}
        class={itemProps.class}
    >
        {@render children?.({})}
    </div>
{/if}
