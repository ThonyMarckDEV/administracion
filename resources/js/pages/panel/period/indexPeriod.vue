<template>
    <Head title="Periodos"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex justify-between items-center mb-4 px-6 mt-4">
                    <toolsPeriod @import-succes="loadingPeriods"/>
                    <Filter @search="searchPeriod"/>
                </div>
                <TablePeriod
                    :period-list="principal.periodList"
                    :period-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal="getIdPeriod"
                    @open-modal-delete="openDeleteModal"
                    :loading="principal.loading"
                />
                <editPeriod 
                    :period-data="principal.periodData"
                    :modal="principal.stateModal.update"
                    @emit-close="closeModal"
                    @update-period="emitUpdatePeriod"
                />
                <Delete
                    :modal="principal.stateModal.delete"
                    :itemId="principal.idPeriod"
                    title="Eliminar periodo"
                    description="¿Está seguro de que desea eliminar este tipo de cliente?"
                    @close-modal="closeModalDelete"
                    @delete-item="emitDeletePeriod"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { BreadcrumbItem } from '@/types';
import { usePeriod } from '@/composables/usePeriod';
import TablePeriod from './components/tablePeriod.vue';
import Delete from '../../../components/delete.vue'
import { onMounted } from 'vue';
import { PeriodUpdateRequest } from './interface/Period';
import editPeriod from './components/editPeriod.vue';
import Filter from '../../../components/filter.vue';
import toolsPeriod from './components/toolsPeriod.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crear un periodo',
        href: '/panel/periods/create',
    },
    {
        title: 'Periodos',
        href: '/panel/periods'
    }
]

onMounted(() => {
    loadingPeriods();
});

const {principal, loadingPeriods, getPeriodById, updatePeriod, deletePeriod} = usePeriod();

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingPeriods(page);
};

// get clientType by id for edit
const getIdPeriod = (id: number) => {
    getPeriodById(id);
};

// close modal
const closeModal = (open: boolean) => {
    principal.stateModal.update = open;
};

// close modal delete
const closeModalDelete = (open: boolean) => {
    principal.stateModal.delete = open;
};

//update clientType
const emitUpdatePeriod = (period: PeriodUpdateRequest, id_period: number) => {
    updatePeriod(id_period, period);
};

// delete clientType
const openDeleteModal = (periodid: number) => {
    principal.stateModal.delete = true;
    principal.idPeriod = periodid;
    console.log('Eliminar periodo con ID:', periodid);
};

// delete clientType
const emitDeletePeriod = (periodid: number | string) => {
    deletePeriod(Number(periodid));
};

// search Service
const searchPeriod = (text: string) => {
    loadingPeriods(1, text);
};
</script>

<style scoped lang="css">
</style>