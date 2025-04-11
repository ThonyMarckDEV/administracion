<template>
    <div class="container mx-auto px-0 py-6">
        <LoadingTable v-if="loading" :headers="6" :row-count="15" />
        <Table v-else class="my-3 w-full overflow-clip rounded-lg border border-gray-100">
            <TableCaption>{{ servicePaginate.current_page }} de {{ servicePaginate.total }}</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-center">ID</TableHead>
                    <TableHead class="w-[250px]">Nombre</TableHead>
                    <TableHead>Costo</TableHead>
                    <TableHead>Fecha de Inicio</TableHead>
                    <TableHead>Estado</TableHead>
                    <TableHead class="text-center">Acciones</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody class="cursor-pointer">
                <TableRow v-for="service in serviceList" :key="service.id">
                    <td class="text-center font-bold">{{ service.id }}</td>
                    <td>{{ service.name }}</td>
                    <td>{{ service.cost.toLocaleString('es-Pe', { style: 'currency', currency: 'PEN' }) }}</td>
                    <td>{{ formatDate(service.ini_date) }}</td>
                    <td class="text-center px-4 py-3">
                        <span
                            v-if="typeof service.state === 'boolean' ? service.state : service.state.toLowerCase() === 'activo'"
                            class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200"
                        >
                            <span class="mr-1 h-2 w-2 rounded-full bg-green-500 dark:bg-green-400"></span>
                            Activo
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 dark:bg-red-900/30 dark:text-red-200"
                        >
                            <span class="mr-1 h-2 w-2 rounded-full bg-red-500 dark:bg-red-400"></span>
                            Inactivo
                        </span>
                    </td>
                    <td class="flex justify-center gap-3 mt-2">
                        <Button variant="outline" class="h-8 w-8 p-0 text-orange-600 hover:bg-orange-50 hover:text-orange-700 dark:text-orange-400 dark:hover:bg-orange-900/30 dark:hover:text-orange-300" @click="openModal(service.id)">
                            <UserPen class="h-5 w-5" />
                        </Button>
                        <Button variant="outline" class="h-8 w-8 p-0 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300" @click="openModalDelete(service.id)">
                            <Trash class="h-5 w-5" />
                        </Button>
                    </td>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationService :meta="servicePaginate" @page-change="$emit('page-change', $event)" />
    </div>
</template>

<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/toast';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Trash, UserPen } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { ServiceResource } from '../interface/Service';
import PaginationService from '../../../../components/pagination.vue';

const { toast } = useToast();

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal', id_service: number): void;
    (e: 'open-modal-delete', id_service: number): void;
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

const { serviceList, servicePaginate, loading } = defineProps<{
    serviceList: ServiceResource[];
    servicePaginate: Pagination;
    loading: boolean;
}>();

const openModal = (id: number) => {
    emit('open-modal', id);
};

const openModalDelete = (id: number) => {
    emit('open-modal-delete', id);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-CL', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

</script>

<style scoped lang="css"></style>
