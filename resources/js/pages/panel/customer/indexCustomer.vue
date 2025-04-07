<template>
    <Head title="Clientes"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex justify-between items-center mb-4 px-6 mt-4">
                    <ToolsCustomer @import-success="loadingCustomers" />
                    <FilterCustomer @search="searchCustomer" />
                </div>
                <TableCustomer
                    :customer-list="principal.customerList"
                    :customer-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal="getIdCustomer"
                    @open-modal-delete="openDeleteModal"
                    :loading="principal.loading"
                />
                <EditCustomer
                    :customer-data="principal.customerData"
                    :modal="principal.statusModal.update"
                    :customer-types="principal.clientTypeList"
                    @close-modal="closeModel"
                    @update-customer="emitUpdateCustomer"
                />
                <DeleteCustomer
                    :modal="principal.statusModal.delete"
                    :itemId="principal.idCustomer"
                    title="Eliminar Servicio"
                    description="¿Está seguro de que desea eliminar este servicio?"
                    @close-modal="closeModalDelete"
                    @delete-item="emitDeleteCustomer"
                />
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import { useCustomer } from '@/composables/useCustomer';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import DeleteCustomer from '../../../components/delete.vue';
import FilterCustomer from '../../../components/filter.vue';
import EditCustomer from './components/editCustomer.vue';
import TableCustomer from './components/tableCustomer.vue';
import { CustomerRequestUpdate } from './interface/Customer';
import ToolsCustomer from './components/toolsCustomer.vue';

const { principal, loadingCustomers, getCustomerById, updateCustomer, deleteCustomer } = useCustomer();

onMounted(() => {
    loadingCustomers();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crear Cliente',
        href: '/panel/customers/create',
    },
    {
        title: 'Clientes',
        href: '/panel/customers',
    },
];

const handlePageChange = (page: number) => {
    loadingCustomers(page);
};

const getIdCustomer = (id: number) => {
    getCustomerById(id);
};

const closeModel = (open: boolean) => {
    principal.statusModal.update = open;
};
const closeModalDelete = (open: boolean) => {
    principal.statusModal.delete = open;
};

const emitUpdateCustomer = (customer: CustomerRequestUpdate, id: number) => {
    updateCustomer(id, customer);
};

const openDeleteModal = (id: number) => {
    principal.statusModal.delete = true;
    principal.idCustomer = id;
};

const emitDeleteCustomer = (id: number | string) => {
    deleteCustomer(Number(id));
};
const searchCustomer = (text: string) => {
    loadingCustomers(1, text);
};
</script>
<style scoped></style>
