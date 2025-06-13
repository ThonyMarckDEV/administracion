<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BanknoteIcon,
    BookOpen,
    CalendarDays,
    CreditCard,
    Folder,
    HandCoins,
    LayoutGrid,
    List,
    Map,
    PackageCheck,
    Percent,
    Settings2,
    Shield,
    Truck,
    UserSquare,
    ListCheck
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];
const mainNavItems3: NavItem[] = [
    {
        title: 'Servicios',
        href: '/panel/services',
        icon: PackageCheck,
    },
    {
        title: 'Descuentos',
        href: '/panel/discounts',
        icon: Percent,
    },
    {
        title: 'Categorías',
        href: '/panel/categories',
        icon: List,
    },
];
const mainNavItems4: NavItem[] = [
    {
        title: 'Clientes',
        href: '#',
        icon: UserSquare,
        items: [
            {
                title: 'Clientes',
                href: '/panel/customers',
            },
            {
                title: 'Tipos de cliente',
                href: '/panel/clientTypes',
            },
        ],
    },
];
const mainNavItems5: NavItem[] = [
    {
        title: 'Proveedores',
        href: '/panel/suppliers',
        icon: Truck,
    },
];
const mainNavItems6: NavItem[] = [
    {
        title: 'Periodo',
        href: '/panel/periods',
        icon: CalendarDays,
    },
    {
        title: 'Plan de pagos',
        href: '/panel/paymentPlans',
        icon: CreditCard,
    },
    {
        title: 'Egresos',
        href: '/panel/amounts',
        icon: BanknoteIcon,
    },
    {
        title: 'Pagos',
        href: '/panel/payments',
        icon: HandCoins,
    },
    {
        title: 'Lista de comprobantes',
        href: '/panel/invoices',
        icon: ListCheck,
    }
];

const mainNavItems2: NavItem[] = [
    {
        title: 'Seguridad',
        href: '#',
        icon: Shield,
        items: [
            {
                title: 'Usuarios',
                href: '/panel/users',
            },
            {
                title: 'Roles',
                href: '#',
            },
            {
                title: 'Permisos',
                href: '#',
            },
        ],
    },
    {
        title: 'Models',
        href: '#',
        icon: Map,
        items: [
            {
                title: 'Genesis',
                href: '#',
            },
            {
                title: 'Explorer',
                href: '#',
            },
            {
                title: 'Quantum',
                href: '#',
            },
        ],
    },
    {
        title: 'Documentation',
        href: '#',
        icon: BookOpen,
        items: [
            {
                title: 'Introduction',
                href: '#',
            },
            {
                title: 'Get Started',
                href: '#',
            },
            {
                title: 'Tutorials',
                href: '#',
            },
            {
                title: 'Changelog',
                href: '#',
            },
        ],
    },
    {
        title: 'Configuraciones',
        href: '#',
        icon: Settings2,
        items: [
            {
                title: 'General',
                href: '/settings/profile',
            },
            {
                title: 'Team',
                href: '#',
            },
            {
                title: 'Billing',
                href: '#',
            },
            {
                title: 'Limits',
                href: '#',
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain
                :items="mainNavItems"
                :items2="mainNavItems2"
                :items3="mainNavItems3"
                :items4="mainNavItems4"
                :items5="mainNavItems5"
                :items6="mainNavItems6"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
