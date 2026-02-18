<script lang="ts">
    import type { Snippet } from 'svelte';
    import { onMount, setContext } from 'svelte';
    import { cn } from '@/lib/utils';
    import { SELECT_CONTEXT, type SelectContext } from './context';

    let {
        name = '',
        value = $bindable(''),
        onValueChange,
        disabled = false,
        required = false,
        class: className = '',
        children,
    }: {
        name?: string;
        value?: string;
        onValueChange?: (value: string) => void;
        disabled?: boolean;
        required?: boolean;
        class?: string;
        children?: Snippet;
    } = $props();

    let open = $state(false);
    let selectElement: HTMLDivElement | null = null;

    const context: SelectContext = {
        open: () => open,
        setOpen: (newValue: boolean) => {
            open = newValue;
        },
        value: () => value,
        setValue: (newValue: string) => {
            value = newValue;
            onValueChange?.(newValue);
        },
    };

    setContext(SELECT_CONTEXT, context);

    onMount(() => {
        const handlePointerDown = (event: PointerEvent) => {
            if (!open || !selectElement) return;

            const target = event.target as Node | null;
            if (target && !selectElement.contains(target)) {
                open = false;
            }
        };

        document.addEventListener('pointerdown', handlePointerDown);

        return () => {
            document.removeEventListener('pointerdown', handlePointerDown);
        };
    });
</script>

<div class={cn('relative', className)} bind:this={selectElement}>
    {@render children?.()}
    <input type="hidden" {name} {value} {required} />
</div>
