<template>
    <Head title="supplier"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <TableSupplier :supplier-list="principal.supplierList" :supplier-paginate="principal.paginacion" @page-change="handlePageChange"/>
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import TableSupplier from './components/tableSupplier.vue';
import { useSupplier } from '@/composables/useSupplier';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];


const {principal,loadingSuppliers} = useSupplier();

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingSuppliers(page);
}
</script>
<style lang="css" scoped></style>