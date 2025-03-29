<template>
    <div class="container mx-auto py-6 px-0">
           <Table class="w-full border border-gray-100 rounded-lg overflow-clip">
                <TableCaption>{{ clientTypePaginate.current_page }} de {{ clientTypePaginate.total }}</TableCaption>
               <TableHeader>
                   <TableRow>
                       <TableHead class="text-center">ID</TableHead>    
                       <TableHead class="w-[250px]">Tipo de cliente</TableHead>
                       <TableHead>Estado</TableHead>
                       <TableHead class="text-center">Acciones</TableHead>
                   </TableRow>
               </TableHeader>
               <TableBody class="cursor-pointer">
                   <TableRow v-for="clientType in clientTypeList" :key="clientType.id">
                       <td class="text-center font-bold">{{ clientType.id }}</td>
                       <td>{{ clientType.name }}</td>
                       <td class="w-[100px] text-center">
                           <span v-if="clientType.state === true" class="bg-green-400 text-white px-2 py-1 rounded-full">Activo</span>
                           <span v-else class="bg-red-400 text-white px-2 py-1 rounded-full">Inactivo</span>
                       </td>
                       <td class="flex gap-2 justify-center">
                        <Button variant="outline" class="bg-orange-400 text-white shadow-md hover:bg-orange-600" @click="openModal(clientType.id)">
                            <UserPen class="h-5 w-5" />
                        </Button>
                        <Button variant="outline" class="bg-red-400 text-white shadow-md hover:bg-red-600" @click="openModalDelete(clientType.id)">
                            <Trash class="h-5 w-5" />
                        </Button>
                       </td>
                   </TableRow>
               </TableBody>
           </Table>
           <PaginationClientType :meta="clientTypePaginate" @page-change="$emit('page-change', $event)" class="mt-4" />
   </div>
</template>
<script setup lang="ts">
import { Pagination } from '@/interface/paginacion';
import { ClientTypeResource } from '../interface/ClientType';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import Button from '@/components/ui/button/Button.vue';
import { usePage } from '@inertiajs/vue3';
import { SharedData } from '@/types';
import { onMounted, ref } from 'vue';
import { useToast } from '@/components/ui/toast';
import PaginationClientType from './paginationClientType.vue';
import { Trash, UserPen } from 'lucide-vue-next';

const { toast } = useToast();

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal', id_clientType: number): void;
    (e: 'open-modal-delete', id_clientType: number): void;
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

const { clientTypeList, clientTypePaginate } = defineProps<{
    clientTypeList: ClientTypeResource[];
    clientTypePaginate: Pagination;
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