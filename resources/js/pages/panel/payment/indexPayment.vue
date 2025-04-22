<template>
    <Head title="Pagos"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="mb-4 mt-4 flex items-center justify-between px-6">
                    <FilterPayments @search="searchPayment" />
                </div>
                <TablePayment
                    :payment-list="principal.paymentList"
                    :payment-paginate="principal.paginacion"
                    :loading="principal.loading"
                    @page-change="handlePageChange"
                />
                <!-- <TableAmount
                    :amounts-list="principal.amountList"
                    :amounts-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal-create="getIdUpdate"
                    @open-modal-delete="getIdDelete"
                    :loading="principal.loading"
                />
                <EditAmount
                    :amount_id="principal.idAmount"
                    :modal="principal.statusModal.update"
                    :amount-data="principal.amountData"
                    @close-modal="closeModalUpdate"
                    @update-amount="getIdAmount"
                >
                </EditAmount>
                <Delete
                    :modal="principal.statusModal.delete"
                    :itemId="principal.idAmount"
                    title="Eliminar egreso"
                    description="¿Está seguro de que desea eliminar este egreso?"
                    @close-modal="closeModalDelete"
                    @delete-item="emitDeleteAmount"
                /> -->
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import FilterPayments from '@/components/filter.vue';
import { usePayment } from '@/composables/usePayment';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import TablePayment from './components/tablePayment.vue';

const { loadingPayments, principal } = usePayment();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nuevo pago',
        href: '/panel/payments/create',
    },
    {
        title: 'Pagos',
        href: '/panel/payments',
    },
];

const handlePageChange = (page: number) => {
    loadingPayments(page);
};

const searchPayment = (text: string) => {
    loadingPayments(1, text);
};

onMounted(() => {
    loadingPayments();
});
</script>
<style scoped></style>
