<template>
    <Head title="Customers"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <FilterUser @search="searchCustomer" />
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
                    @close-modal="closeModel"
                    @update-customer="emitUpdateCustomer"
                />
                <DeleteCustomer
                    :modal="principal.statusModal.delete"
                    :user-id="principal.idCustomer"
                    @close-modal="closeModalDelete"
                    @delete-customer="emitDeleteCustomer"
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
import FilterUser from '../user/components/filterUser.vue';
import DeleteCustomer from './components/deleteCustomer.vue';
import EditCustomer from './components/editCustomer.vue';
import TableCustomer from './components/tableCustomer.vue';
import { CustomerRequestUpdate } from './interface/Customer';

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
        title: 'Exportar a Excel',
        href: '#',
        download: true,
    },
    {
        title: 'Exportar a PDF',
        href: '#',
        download: true,
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

const emitDeleteCustomer = (id: number) => {
    deleteCustomer(id);
};
const searchCustomer = (text: string) => {
    loadingCustomers(1, text);
};
</script>
<style scoped></style>
