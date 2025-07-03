<template>
    <div class="container mx-auto px-4 py-2">
        <LoadingTable v-if="loading" :headers="6" :row-count="10" />
        <div v-else class="space-y-4">         
           <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm dark:border-gray-700 dark:shadow-none">
            <Table class="w-full"> 
                <TableHeader class="bg-gray-50 dark:bg-gray-800/50">
                    <TableRow class="hover:bg-transparent">
<TableHead
    @click="sortBy('id')"
    class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none"
>
    <div class="inline-flex items-center space-x-1">
        <span>ID</span>
        <span v-if="sortField === 'id'">
            <span v-if="sortDirection === 'asc'">▲</span>
            <span v-else>▼</span>
        </span>
    </div>
</TableHead>

<TableHead
    @click="sortBy('name')"
    class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none"
>
    <div class="inline-flex items-center space-x-1">
        <span>NOMBRE</span>
        <span v-if="sortField === 'name'">
            <span v-if="sortDirection === 'asc'">▲</span>
            <span v-else>▼</span>
        </span>
    </div>
</TableHead>
                        <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">COSTO</TableHead>
                        <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">FECHA DE INICIO</TableHead>
                        <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">ESTADO</TableHead>
                        <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">ACCIONES</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <TableRow 
                        v-for="service in serviceList" 
                        :key="service.id"  
                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/30"
                    >
                        <TableCell class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ service.id }}</TableCell>
                        <TableCell class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ service.name }}</TableCell>
                        <TableCell class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ service.cost.toLocaleString('es-PE', { style: 'currency', currency: 'PEN' }) }}</TableCell>
                        <TableCell class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ formatDate(service.ini_date) }}</TableCell>
                        <TableCell class="px-4 py-3">
                            <span
                                v-if="typeof service.state === 'boolean' ? service.state : service.state === 'activo'"
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
                        </TableCell>
                        <TableCell class="px-4 py-3">
                            <div class="flex justify-start pl-4 space-x-2">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-8 w-8 p-0 text-orange-600 hover:bg-orange-50 hover:text-orange-700 dark:text-orange-400 dark:hover:bg-orange-900/30 dark:hover:text-orange-300"
                                    @click="openModal(service.id)"
                                    title="Editar cliente"
                                >
                                    <UserPen class="h-4 w-4" />
                                    <span class="sr-only">Editar cliente</span>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-8 w-8 p-0 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300"
                                    @click="openModalDelete(service.id)"
                                    title="Eliminar cliente"
                                >
                                    <Trash class="h-4 w-4" />
                                    <span class="sr-only">Eliminar cliente</span>
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
           </div>
           <PaginationService :meta="servicePaginate" @page-change="$emit('page-change', $event)" />
        </div>
    </div>
</template>

<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/toast';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Trash, UserPen } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import PaginationService from '../../../../components/pagination.vue';
import { ServiceResource } from '../interface/Service';

const { toast } = useToast();


// Control del ordenamiento
const sortField = ref('');
const sortDirection = ref<'asc' | 'desc'>('asc');

const sortBy = (field: keyof ServiceResource) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    serviceList.sort((a, b) => {
        const aVal = String(a[field]).toLowerCase?.() || '';
        const bVal = String(b[field]).toLowerCase?.() || '';
        
        if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1;
        if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1;
        return 0;
    });
};


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