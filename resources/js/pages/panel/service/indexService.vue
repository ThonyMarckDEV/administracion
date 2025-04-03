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
            :itemId="principal.idService"
            title="Eliminar Servicio"
            description="¿Está seguro de que desea eliminar este servicio?"
            @close-modal="closeModalDelete"
            @delete-item="emitDeleteService"
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
import DeleteService from '../../../components/delete.vue';
import EditService from './components/editService.vue';
import TableService from './components/tableService.vue';
import FilterService from '../../../components/filter.vue';
import { ServiceUpdateRequest } from './interface/Service';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crear servicio',
        href: '/panel/services/create',
    },
    {
        title: 'Exportar a Excel',
        href: '/panel/reports/export-excel-services',
        download: true,
    },
    {
        title: 'Exportar a PDF',
        href: '/panel/reports/export-pdf-services',
        download: true,
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


// search Service
const searchService = (text: string) => {
    loadingServices(1, text);
};

const emitDeleteService = (serviceId: number | string) => {
  deleteService(Number(serviceId));
};
</script>

<style lang="css" scoped></style>