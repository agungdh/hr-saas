<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    let { class: className = '', children, ...rest }: { class?: string; children?: Snippet; [key: string]: unknown } = $props();
</script>

<thead class={cn('[&_tr]:border-b', className)} {...rest}>
    {@render children?.()}
</thead>
