<template>
    <Head title="Pagos"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
<div class="mb-4 mt-4 flex justify-between px-6 mr-7">
    <!-- ToolsPayments alineado a la izquierda -->
    <ToolsPayments @import-success="loadingPayments" />

    <!-- Filtros alineados a la derecha -->
    <div class="flex items-center gap-x-4">
        <FilterPayments @search="searchPayment" />
        <StatusFilter @search="searchStatus" />
    </div>
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
                    title="Eliminar ingreso"
                    description="¿Está seguro de que desea eliminar este ingreso?"
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
import StatusFilter from '@/components/ListFilter.vue';
import { usePayment } from '@/composables/usePayment';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { toast } from '@/components/ui/toast'; 
import EditPayment from './components/editPayment.vue';
import TablePayment from './components/tablePayment.vue';
import { updatePayment } from './interface/Payment';
import { PaymentServices } from '@/services/paymentServices';
import ToolsPayments from './components/toolsPayment.vue';


const { loadingPayments, showPayment, principal, showPaymentData, updatePaymentF, deletePayment } = usePayment();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pagos',
        href: '/panel/payments',
    },
];

// 🔸 filtros activos
const filterCustomer = ref('');
const filterStatus = ref('');

// 🔸 función local que reemplaza al loadingPayments del composable
const loadingPaymentsWrapper = async (page: number = 1) => {
    try {
        principal.loading = true;
        const response = await PaymentServices.index(page, filterCustomer.value, filterStatus.value);
        principal.paymentList = response.payments;
        principal.paginacion = response.pagination;
    } catch (error) {
        console.error('Error loading payments:', error);
    } finally {
        principal.loading = false;
    }
};

// 🔸 eventos
const handlePageChange = (page: number) => {
    loadingPaymentsWrapper(page);
};

const searchPayment = (text: string) => {
    filterCustomer.value = text;
    loadingPaymentsWrapper(1);
};

const searchStatus = (status: string) => {
    filterStatus.value = status;
    loadingPaymentsWrapper(1);
};

const closeModalUpdate = () => {
    principal.statusModalUpdate = false;
};

const getIdUpdate = (id: number) => {
    showPayment(id);
};

const getIdDelete = (id: number) => {
    principal.statusModalDelete = true;
    principal.payment_id_delete = id;
};

const emitDeletePayment = (id: number | string) => {
    principal.statusModalDelete = false;
    deletePayment(Number(id));
};

const clouseModalDelete = () => {
    principal.statusModalDelete = false;
};

const dataUpdatePayment = async (data: updatePayment, id: number) => {
    try {
        const response = await updatePaymentF(data, id);
        toast({
            title: 'Pago actualizado',
            description: 'El pago se actualizó correctamente',
        });
    } catch (error: unknown) {
        let message = 'Error al actualizar el pago';
        if (axios.isAxiosError(error)) {
            message = error.response?.data?.message || message;
        }

        toast({
            title: 'Error',
            description: message,
        });
    }
};

// cargar datos al iniciar
onMounted(() => {
    loadingPaymentsWrapper();
});
</script>

<style scoped></style>
