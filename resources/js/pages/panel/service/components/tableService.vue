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
                    <td class="text-center">
                        <span :class="['rounded-full px-2 py-1 text-white', getStateDisplay(service.state).color]">
                            {{ getStateDisplay(service.state).text }}
                        </span>
                    </td>
                    <td class="flex justify-center gap-2">
                        <Button variant="outline" class="bg-orange-400 text-white shadow-md hover:bg-orange-600" @click="openModal(service.id)">
                            <UserPen class="h-5 w-5" />
                        </Button>
                        <Button variant="outline" class="bg-red-400 text-white shadow-md hover:bg-red-600" @click="openModalDelete(service.id)">
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

const getStateDisplay = (state: boolean | string): { text: string; color: string } => {
    const stateString = typeof state === 'boolean' ? (state ? 'Activo' : 'Inactivo') : state.toLowerCase() === 'activo' ? 'Activo' : 'Inactivo';

    return {
        text: stateString,
        color: stateString === 'Activo' ? 'bg-green-400' : 'bg-red-400',
    };
};
</script>

<style scoped lang="css"></style>
