<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BarChart3,
    Boxes,
    Building2,
    Clock,
    LayoutGrid,
    Package,
    ReceiptText,
    ShoppingCart,
    Tags,
    Truck,
    Users,
    UsersRound,
    Warehouse,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as branches } from '@/routes/branches';
import { index as catalog } from '@/routes/catalog';
import { index as customers } from '@/routes/customers';
import { index as inventory } from '@/routes/inventory';
import { index as pos } from '@/routes/pos';
import { index as products } from '@/routes/products';
import { index as purchaseOrders } from '@/routes/purchase-orders';
import { index as reports } from '@/routes/reports';
import { index as sales } from '@/routes/sales';
import { index as shifts } from '@/routes/shifts';
import { index as suppliers } from '@/routes/suppliers';
import { index as users } from '@/routes/users';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    { title: 'POS', href: pos(), icon: ShoppingCart },
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Sales', href: sales(), icon: ReceiptText },
    { title: 'Shifts', href: shifts(), icon: Clock },
    { title: 'Products', href: products(), icon: Package },
    { title: 'Catalog', href: catalog(), icon: Tags },
    { title: 'Inventory', href: inventory(), icon: Warehouse },
    { title: 'Customers', href: customers(), icon: Users },
    { title: 'Suppliers', href: suppliers(), icon: Truck },
    { title: 'Purchasing', href: purchaseOrders(), icon: Boxes },
    { title: 'Reports', href: reports(), icon: BarChart3 },
    { title: 'Users', href: users(), icon: UsersRound },
    { title: 'Branches', href: branches(), icon: Building2 },
];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
