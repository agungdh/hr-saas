<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { Form } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { Link } from '@inertiajs/svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import InputError from '@/components/InputError.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';

    const breadcrumbItems: BreadcrumbItem[] = [
        {
            title: 'Tenants',
            href: '/admin/tenants',
        },
        {
            title: 'Create',
            href: '/admin/tenants/create',
        },
    ];

    let selectedPlan = $state('free');
</script>

<AppHead title="Create Tenant" />

<AppLayout breadcrumbs={breadcrumbItems}>
    <div class="space-y-6 px-4 py-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <h1 class="text-2xl font-semibold tracking-tight">Create Tenant</h1>
                <p class="text-sm text-muted-foreground">Add a new tenant to your SaaS platform</p>
            </div>
        </div>

        <div class="max-w-2xl">
            <Form action="/admin/tenants" method="POST" class="space-y-6">
                {#snippet children({ errors, processing })}
                    <div class="grid gap-2">
                        <Label for="id">Tenant ID</Label>
                        <Input
                            id="id"
                            name="id"
                            class="mt-1 block w-full"
                            required
                            placeholder="e.g., acme-corp"
                        />
                        <InputError class="mt-2" message={errors.id} />
                        <p class="text-xs text-muted-foreground">
                            Unique identifier for the tenant (used for database schema)
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Tenant Name</Label>
                        <Input
                            id="name"
                            name="name"
                            class="mt-1 block w-full"
                            required
                            placeholder="e.g., Acme Corporation"
                        />
                        <InputError class="mt-2" message={errors.name} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="slug">Slug</Label>
                        <Input
                            id="slug"
                            name="slug"
                            class="mt-1 block w-full"
                            required
                            placeholder="e.g., acme"
                        />
                        <InputError class="mt-2" message={errors.slug} />
                        <p class="text-xs text-muted-foreground">
                            URL-friendly identifier for the tenant
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="domain">Domain</Label>
                        <Input
                            id="domain"
                            name="domain"
                            class="mt-1 block w-full"
                            required
                            placeholder="e.g., acme.hr-saas.test"
                        />
                        <InputError class="mt-2" message={errors.domain} />
                        <p class="text-xs text-muted-foreground">
                            Subdomain for accessing this tenant
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="plan">Plan</Label>
                        <Select name="plan" value={selectedPlan} onValueChange={(v) => selectedPlan = v}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select a plan" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="free">Free</SelectItem>
                                <SelectItem value="pro">Pro</SelectItem>
                                <SelectItem value="enterprise">Enterprise</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" message={errors.plan} />
                    </div>

                    <div class="grid gap-2">
                        <Label for="trial_ends_at">Trial End Date (Optional)</Label>
                        <Input
                            id="trial_ends_at"
                            name="trial_ends_at"
                            type="date"
                            class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" message={errors.trial_ends_at} />
                        <p class="text-xs text-muted-foreground">
                            When the trial period ends (leave empty for no trial)
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="subscription_ends_at">Subscription End Date (Optional)</Label>
                        <Input
                            id="subscription_ends_at"
                            name="subscription_ends_at"
                            type="date"
                            class="mt-1 block w-full"
                        />
                        <InputError class="mt-2" message={errors.subscription_ends_at} />
                        <p class="text-xs text-muted-foreground">
                            When the paid subscription ends (leave empty for free plan)
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" disabled={processing}>
                            Create Tenant
                        </Button>
                        <TextLink href="/admin/tenants">Cancel</TextLink>
                    </div>
                {/snippet}
            </Form>
        </div>
    </div>
</AppLayout>
