<template>
    <div class="container mx-auto px-0">
        <LoadingTable v-if="loading" :headers="5" :row-count="10" />
        <Table v-else class="my-3 w-full overflow-clip rounded-lg border border-gray-100">
            <TableCaption>{{ discountPaginate.current_page }} de {{ discountPaginate.total }}</TableCaption>
            <TableHeader>
                   <TableRow>
                       <TableHead class="text-center">ID</TableHead>    
                       <TableHead class="w-[250px]">Descripción</TableHead>
                       <TableHead class="text-left px-10">Porcentaje</TableHead>
                       <TableHead>Estado</TableHead>
                       <TableHead class="text-center">Acciones</TableHead>
                   </TableRow>
               </TableHeader>
               <TableBody class="cursor-pointer">
                   <TableRow v-for="discount in discountList" :key="discount.id">
                       <td class="text-center font-bold">{{ discount.id }}</td>
                       <td>{{ discount.description }}</td>
                        <td class="text-left px-10">{{ discount.percentage }} %</td>
                       <td class="w-[100px] text-center">
                        <span v-if="discount.state === true" class="rounded-full bg-green-400 px-2 py-1 text-white">Activo</span>
                        <span v-else class="rounded-full bg-red-400 px-2 py-1 text-white">Inactivo</span>
                       </td>
                       <td class="flex gap-2 justify-center">
                        <Button variant="outline" class="bg-orange-400 text-white shadow-md hover:bg-orange-600" @click="openModal(discount.id)">
                            <UserPen class="h-5 w-5" />
                        </Button>
                        <Button variant="outline" class="bg-red-400 text-white shadow-md hover:bg-red-600" @click="openModalDelete(discount.id)">
                            <Trash class="h-5 w-5" />
                        </Button>
                       </td>
                   </TableRow>
               </TableBody>
           </Table>
           <PaginationDiscount :meta="discountPaginate" @page-change="$emit('page-change', $event)"/>
   </div>
</template>
<script setup lang="ts">

//import LoadingTable from '@/components/loadingTable.vue';
import { Pagination } from '@/interface/paginacion';
import { DiscountResource } from '../interface/Discount';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import Button from '@/components/ui/button/Button.vue';
import { usePage } from '@inertiajs/vue3';
import { SharedData } from '@/types';
import { onMounted, ref } from 'vue';
import { useToast } from '@/components/ui/toast';
import PaginationDiscount from '../../../../components/pagination.vue';
import { Trash, UserPen } from 'lucide-vue-next';

const { toast }  = useToast();

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal', id_discount: number): void;
    (e: 'open-modal-delete', id_discount: number): void;
}>();
const page = usePage<SharedData>();

const message = ref(page.props.flash?.message || '');

onMounted(() => {
    if (message.value) {
        toast({
            title: 'Notificación',
            description: message.value,
        });
    }
});

const {discountList,discountPaginate,loading} = defineProps<{
   discountList: DiscountResource[];
   discountPaginate: Pagination;
   loading: boolean;
}>();

const openModal = (id: number) => {
    emit('open-modal', id);
};

const openModalDelete = (id: number) => {
    emit('open-modal-delete', id);
};

</script>
<style scoped lang="css"></style>