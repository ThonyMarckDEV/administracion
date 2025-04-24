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
                    @open-modal-create="getIdUpdate"
                    @open-modal-delete="getIdDelete"
                />

                <EditPayment
                    v-if="showPaymentData"
                    :payment-data="showPaymentData"
                    :status-modal="principal.statusModalUpdate"
                    @close-modal="closeModalUpdate"
                    @update-payment="dataUpdatePayment"
                />
                <Delete
                    :modal="principal.statusModalDelete"
                    :itemId="principal.payment_id_delete"
                    title="Eliminar ingreso"
                    description="¿Está seguro de que desea eliminar este ingreso?"
                    @close-modal="clouseModalDelete"
                    @delete-item="emitDeletePayment"
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
import Delete from '@/components/delete.vue';
import FilterPayments from '@/components/filter.vue';
import { usePayment } from '@/composables/usePayment';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import EditPayment from './components/editPayment.vue';
import TablePayment from './components/tablePayment.vue';
import { updatePayment } from './interface/Payment';

const { loadingPayments, showPayment, principal, showPaymentData, updatePaymentF, deletePayment } = usePayment();

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

// emit events in component TablePayment
const handlePageChange = (page: number) => {
    loadingPayments(page);
};

// emit events in component EditPayment
const closeModalUpdate = () => {
    principal.statusModalUpdate = false;
};
const searchPayment = (text: string) => {
    loadingPayments(1, text);
};

const getIdUpdate = (id: number) => {
    showPayment(id);
};

const getIdDelete = (id: number) => {
    principal.statusModalDelete = true;
    principal.payment_id_delete = id;
    console.log('eliminar' + id);
};
const emitDeletePayment = (id: number | string) => {
    principal.statusModalDelete = false;
    console.log('emitDeletePayment', id);
    deletePayment(Number(id));
};
const clouseModalDelete = () => {
    principal.statusModalDelete = false;
};

// get data from editPayment
const dataUpdatePayment = (data: updatePayment, id: number) => {
    console.log('dataUpdatePayment', data);
    updatePaymentF(data, id);
    console.log('id', id);
};

onMounted(() => {
    loadingPayments();
});
</script>
<style scoped></style>
