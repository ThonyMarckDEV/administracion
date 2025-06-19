<template>
    <Head title="Comprobantes"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="mb-4 mt-4 flex items-center justify-between px-6">
                    <FilterInvoices @search="searchInvoice" />
                </div>
                <TableInvoice
                    :invoice-list="principal.invoiceList"
                    :invoice-paginate="principal.invoicePaginate"
                    :loading="principal.loading"
                    @page-change="handleInvoicePageChange"
                    @open-modal-show="getIdShow"
                    @refresh-list="handleRefreshList"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import FilterInvoices from '@/components/filter.vue';
import { useInvoice } from '@/composables/useInvoice';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import TableInvoice from './components/TableInvoice.vue';

const { loadInvoices, showInvoice, principal } = useInvoice();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Comprobantes',
        href: '/panel/invoices',
    },
];

const handleInvoicePageChange = (page: number) => {
    loadInvoices(page);
};

const searchInvoice = (text: string) => {
    loadInvoices(1, text);
};

const getIdShow = (id: number) => {
    showInvoice(id);
};

const handleRefreshList = () => {
    loadInvoices(principal.invoicePaginate.current_page);
};

onMounted(() => {
    loadInvoices();
});
</script>