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
                    @open-modal-annul="getIdAnnul"
                />
                <Delete
                    :modal="principal.statusModalAnnul"
                    :itemId="principal.invoiceIdAnnul"
                    title="Anular Comprobante"
                    description="¿Está seguro de que desea anular este comprobante?"
                    @close-modal="closeModalAnnul"
                    @delete-item="emitAnnulInvoice"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import Delete from '@/components/delete.vue';
import FilterInvoices from '@/components/filter.vue';
import { useInvoice } from '@/composables/useInvoice';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import TableInvoice from './components/TableInvoice.vue';

const { loadInvoices, showInvoice, principal, showInvoiceData, annulInvoice } = useInvoice();

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

const getIdAnnul = (id: number) => {
    principal.statusModalAnnul = true;
    principal.invoiceIdAnnul = id;
};

const emitAnnulInvoice = (id: number) => {
    principal.statusModalAnnul = false;
    annulInvoice(id);
};

const closeModalAnnul = () => {
    principal.statusModalAnnul = false;
};

onMounted(() => {
    loadInvoices();
});
</script>