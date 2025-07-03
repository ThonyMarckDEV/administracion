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
    SunMoon,
    Truck,
    User,
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
        title: 'Personal',
        href: '#',
        icon: User,
        items: [
            {
                title: 'Usuarios',
                href: '/panel/users',
            },
            //{
              //  title: 'Roles',
                //href: '#',
            //},
            //{
              //  title: 'Permisos',
                //href: '#',
            //},
        ],
    },
];
const mainNavItems7: NavItem[] = [
     {
        title: 'Apariencia',
        href: '/settings/appearance',
        icon: SunMoon,
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
                :items7="mainNavItems7"

            />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
