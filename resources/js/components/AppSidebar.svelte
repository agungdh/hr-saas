<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { Link } from '@inertiajs/svelte';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import Users from 'lucide-svelte/icons/users';
    import Building2 from 'lucide-svelte/icons/building-2';
    import Calendar from 'lucide-svelte/icons/calendar';
    import Wallet from 'lucide-svelte/icons/wallet';
    import User from 'lucide-svelte/icons/user';
    import Palette from 'lucide-svelte/icons/palette';
    import Key from 'lucide-svelte/icons/key';
    import Building from 'lucide-svelte/icons/building';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
        SidebarGroup,
        SidebarGroupLabel,
        SidebarMenu as SidebarMenuInner,
        SidebarMenuButton as SidebarMenuButtonInner,
        SidebarMenuItem as SidebarMenuItemInner,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import type { NavItem } from '@/types';
    import { currentUrlState } from '@/lib/currentUrl';
    import tenant from '@/routes/tenant';
    import profile from '@/routes/profile';
    import appearance from '@/routes/appearance';
    import userPassword from '@/routes/user-password';
    import admin from '@/routes/admin';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const { currentUrl, isCurrentUrl } = currentUrlState();

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: tenant.dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Employees',
            href: tenant.employees.index(),
            icon: Users,
        },
        {
            title: 'Departments',
            href: tenant.departments.index(),
            icon: Building2,
        },
        {
            title: 'Leave',
            href: tenant.leave.index(),
            icon: Calendar,
        },
        {
            title: 'Payroll',
            href: tenant.payroll.index(),
            icon: Wallet,
        },
    ];

    const settingsNavItems: NavItem[] = [
        {
            title: 'Profile',
            href: profile.edit(),
            icon: User,
        },
        {
            title: 'Appearance',
            href: appearance.edit(),
            icon: Palette,
        },
        {
            title: 'Password',
            href: userPassword.edit(),
            icon: Key,
        },
    ];

    const adminNavItems: NavItem[] = [
        {
            title: 'Tenants',
            href: admin.tenants.index(),
            icon: Building,
        },
    ];

    const isSuperAdmin = $derived($page.props.auth?.isSuperAdmin ?? false);
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild>
                    {#snippet children(props)}
                        <Link {...props} href={toUrl(tenant.dashboard())} class={props.class}>
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} label="Platform" />

        <SidebarGroup class="px-2 py-0">
            <SidebarGroupLabel>Settings</SidebarGroupLabel>
            <SidebarMenuInner>
                {#each settingsNavItems as item (toUrl(item.href))}
                    <SidebarMenuItemInner>
                        <SidebarMenuButtonInner asChild isActive={isCurrentUrl(item.href, $currentUrl)} tooltip={item.title}>
                            {#snippet children(props)}
                                <Link {...props} href={toUrl(item.href)} class={props.class}>
                                    {#if item.icon}
                                        <item.icon class="size-4 shrink-0" />
                                    {/if}
                                    <span>{item.title}</span>
                                </Link>
                            {/snippet}
                        </SidebarMenuButtonInner>
                    </SidebarMenuItemInner>
                {/each}
            </SidebarMenuInner>
        </SidebarGroup>

        {#if isSuperAdmin}
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Admin</SidebarGroupLabel>
                <SidebarMenuInner>
                    {#each adminNavItems as item (toUrl(item.href))}
                        <SidebarMenuItemInner>
                            <SidebarMenuButtonInner asChild isActive={isCurrentUrl(item.href, $currentUrl)} tooltip={item.title}>
                                {#snippet children(props)}
                                    <Link {...props} href={toUrl(item.href)} class={props.class}>
                                        {#if item.icon}
                                            <item.icon class="size-4 shrink-0" />
                                        {/if}
                                        <span>{item.title}</span>
                                    </Link>
                                {/snippet}
                            </SidebarMenuButtonInner>
                        </SidebarMenuItemInner>
                    {/each}
                </SidebarMenuInner>
            </SidebarGroup>
        {/if}
    </SidebarContent>

    <SidebarFooter>
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
