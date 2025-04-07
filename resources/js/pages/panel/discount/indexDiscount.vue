<template>
    <Head title="Descuentos"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <FilterDiscount @search="searchDiscount" />
                <TableDiscount
                    :discount-list="principal.discountList"
                    :discount-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal="getIdDiscount"
                    @open-modal-delete="openDeleteModal"
                    :loading="principal.loading"
                />
                <EditDiscount
                    :discount-data="principal.discountData"
                    :modal="principal.stateModal.update"
                    @emit-close="closeModal"
                    @update-discount="emitUpdateDiscount"
                />
                <DeleteDiscount
                    :modal="principal.stateModal.delete"
                    :itemId="principal.idDiscount"
                    title="Eliminar Servicio"
                    description="¿Está seguro de que desea eliminar este servicio?"
                    @close-modal="closeModalDelete"
                    @delete-item="emitDeleteDiscount"
                />
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import TableDiscount from './components/tableDiscount.vue';
import { DiscountUpdateRequest } from './interface/Discount';
import DeleteDiscount from '../../../components/delete.vue';
import EditDiscount from './components/editDiscount.vue';
import FilterDiscount from '../../../components/filter.vue';
import { useDiscount } from '@/composables/useDiscount';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
{
        title: 'Crear descuento',
        href: '/panel/discounts/create',
    },
    {
        title: 'Exportar',
        href: '/panel/discounts/export',
    },
    {
        title: 'Descuentos',
        href: '/panel/discounts',
    },
];

onMounted(() => {
    loadingDiscounts();
});

const {principal,loadingDiscounts, getDiscountById, updateDiscount, deleteDiscount} = useDiscount();

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingDiscounts(page);
};
// get Discount by id for edit
const getIdDiscount = (id: number) => {
    getDiscountById(id);
};
// close modal
const closeModal = (open: boolean) => {
    principal.stateModal.update = open;
};
// close modal delete
const closeModalDelete = (open: boolean) => {
    principal.stateModal.delete = open;
};

// update Discount
const emitUpdateDiscount = (discount: DiscountUpdateRequest, id_discount: number) => {
    updateDiscount(id_discount, discount);
};

// delete discount
const openDeleteModal = (discountId: number) => {
    principal.stateModal.delete = true;
    principal.idDiscount = discountId;
    console.log('Eliminar descuento con ID:', discountId);
};
// delete discount
const emitDeleteDiscount = (discountId: number | string) => {
    deleteDiscount(Number(discountId));
};
// search user
const searchDiscount = (text: string) => {
    loadingDiscounts(1, text);
};
</script>
<style lang="css" scoped></style>