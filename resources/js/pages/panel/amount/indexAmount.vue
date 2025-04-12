<template>
    <Head title="egresos"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="mb-4 mt-4 flex items-center justify-between px-6">
                    <!-- <ToolsCustomer @import-success="loadingCustomers" />
                    <FilterCustomer @search="searchCustomer" /> -->
                </div>
                <TableAmount
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
                />
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import Delete from '@/components/delete.vue';
import { useAmount } from '@/composables/useAmount';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import EditAmount from './components/editAmount.vue';
import TableAmount from './components/tableAmount.vue';
import { AmountResponseShow, AmountUpdatePayload } from './interface/Amount';

const { principal, loadingAmounts, deleteAmount, updateAmount, loadingShowAmount } = useAmount();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nuevo egreso',
        href: '/panel/amounts/create',
    },
    {
        title: 'Egresos',
        href: '/panel/amounts',
    },
];

onMounted(() => {
    loadingAmounts();
});

const handlePageChange = (page: number) => {
    loadingAmounts(page);
};

const getIdUpdate = (id: number) => {
    console.log('actualizar' + id);
    principal.statusModal.update = true;
    loadingShowAmount(id);
};

const getIdDelete = (id: number) => {
    principal.idAmount = id;
    principal.statusModal.delete = true;
    console.log('eliminar' + id);
};

// close modal delete
const closeModalDelete = () => {
    principal.statusModal.delete = false;
};
// emit delete amount
const emitDeleteAmount = (id: number | string) => {
    console.log('eliminar' + id);
    deleteAmount(Number(id));
};
// close modal update
const closeModalUpdate = () => {
    principal.statusModal.update = false;
    principal.idAmount = 0;
};
// get id amount
const getIdAmount = (amount: AmountResponseShow, id: number) => {
    const payload: AmountUpdatePayload = {
        category_id: amount.category_id,
        supplier_id: amount.supplier_id,
        description: amount.description,
        amount: amount.amount,
        date_init: amount.date_init,
    };

    updateAmount(id, payload);
    console.log('id', id);
    console.log('actualizar', payload);
};
</script>
<style scoped></style>
