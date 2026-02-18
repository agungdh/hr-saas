<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    let { class: className = '', children, ...rest }: { class?: string; children?: Snippet; [key: string]: unknown } = $props();
</script>

<tfoot
    class={cn(
        'border-t bg-muted/50 font-medium [&>tr]:last:border-b-0',
        className,
    )}
    {...rest}
>
    {@render children?.()}
</tfoot>
