<template>
    <Head title="Plan de Pagos"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex justify-between items-center mb-4 px-6 mt-4">
                 <ToolsPaymentPlan @import-success="loadingPaymentPlan" />
                    <FilterPaymentPlan @search="searchPaymentPlan" />
                </div>
                <TablePaymentPlan
                    :payment-plan-list="principal.paymentPlanList"
                    :payment-plan-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal="getIdPaymentPlan"
                    @open-modal-delete="openDeleteModal"
                    :loading="principal.loading"
                />
                <EditPaymentPlan
                    :payment-plan-data="principal.paymentPlanData"
                    :modal="principal.stateModal.update"
                    :payment-plan-service="principal.serviceList"
                    :payment-plan-customer="principal.customerList"
                    :payment-plan-period="principal.periodList"
                    @close-modal="closeModel"
                    @update-payment-plan="emitUpdatePaymentPlan"
                />
                <DeletePaymentPlan
                    :modal="principal.stateModal.delete"
                    :itemId="principal.idPaymentPlan"
                    title="Eliminar plan de pago"
                    description="¿Está seguro de que desea eliminar este plan de pago?"
                    @close-modal="closeModalDelete"
                    @delete-item="emitDeletePaymentPlan"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import { onMounted } from 'vue';
import TablePaymentPlan from './components/tablePaymentPlan.vue';
import FilterPaymentPlan from '../../../components/filter.vue';
import DeletePaymentPlan from '../../../components/delete.vue';
import EditPaymentPlan from './components/editPaymentPlan.vue';
import { PaymentPlanRequestUpdate } from './interface/PaymentPlan';
import { usePaymentPlan } from '@/composables/usePaymentPlan';
import ToolsPaymentPlan from './components/toolsPaymentPlan.vue';


const { principal, loadingPaymentPlan, getPaymentPlanById, updatePaymentPlan, deletePaymentPlan } = usePaymentPlan();

onMounted(() => {
    loadingPaymentPlan();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crear plan de pago',
        href: '/panel/paymentPlans/create',
    },
    {
        title: 'Plan de pagos',
        href: '/panel/paymentPlans',
    },
];

const handlePageChange = (page: number) => {
    loadingPaymentPlan(page);
};

const getIdPaymentPlan = (id: number) => {
    getPaymentPlanById(id);
};

const closeModel = (open: boolean) => {
    principal.stateModal.update = open;
};
const closeModalDelete = (open: boolean) => {
    principal.stateModal.delete = open;
};

const emitUpdatePaymentPlan = (paymentPlan: PaymentPlanRequestUpdate, id: number) => {
    updatePaymentPlan(id, paymentPlan);
};

const openDeleteModal = (id: number) => {
    principal.stateModal.delete = true;
    principal.idPaymentPlan = id;
};

const emitDeletePaymentPlan = (id: number | string) => {
    deletePaymentPlan(Number(id));
};
const searchPaymentPlan = (text: string) => {
    loadingPaymentPlan(1, text);
};
</script>

<style>
</style>