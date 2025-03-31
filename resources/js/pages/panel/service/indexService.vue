<template>
    <Head title="servicios"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <FilterService @search="searchService" />
                <TableService
                    :service-list="principal.serviceList"
                    :service-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal="getIdService"
                    @open-modal-delete="openDeleteModal"
                    :loading="principal.loading"
                />
                <EditService
                    :service-data="principal.serviceData"
                    :modal="principal.statusModal.update"
                    @emit-close="closeModal"
                    @update-service="emitUpdateService"
                />
                <DeleteService
                    :modal="principal.statusModal.delete"
                    :service-id="principal.idService"
                    @close-modal="closeModalDelete"
                    @delete-service="emitDeleteService"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useService } from '@/composables/useService'; // Assuming you'll create this similar to useUser
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import DeleteService from './components/deleteService.vue';
import EditService from './components/editService.vue';
import TableService from './components/tableService.vue';
import FilterService from './components/filterService.vue';
import { ServiceUpdateRequest } from './interface/Service'; // You'll need to create this interface


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crear servicio',
        href: '/panel/services/create',
    },
    {
        title: 'Exportar',
        href: '/panel/services/export',
    },
    {
        title: 'Servicios',
        href: '/panel/services',
    },
];

onMounted(() => {
    loadingServices();
});

const { 
    principal, 
    loadingServices, 
    getServiceById, 
    updateService, 
    deleteService 
} = useService(); // You'll need to implement this composable

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingServices(page);
};

// get service by id for edit
const getIdService = (id: number) => {
    getServiceById(id);
};

// close modal
const closeModal = (open: boolean) => {
    principal.statusModal.update = open;
};

// close modal delete
const closeModalDelete = (open: boolean) => {
    principal.statusModal.delete = open;
};

// update service
const emitUpdateService = (service: ServiceUpdateRequest, id_service: number) => {
    updateService(id_service, service);
};

// delete service
const openDeleteModal = (serviceId: number) => {
    principal.statusModal.delete = true;
    principal.idService = serviceId;
    console.log('Eliminar servicio con ID:', serviceId);
};

// delete service
const emitDeleteService = (serviceId: number) => {
    deleteService(serviceId);
};

// search Service
const searchService = (text: string) => {
    loadingServices(1, text);
};
</script>

<style lang="css" scoped></style>