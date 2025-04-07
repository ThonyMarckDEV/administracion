<template>
    <div class="container mx-auto px-4 py-2">
        <LoadingTable v-if="loading" :headers="7" :row-count="10" />
            <div v-else class="space-y-4">
                <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm dark:border-gray-700 dark:shadow-none">
                    <Table class="w-full">
                        <TableHeader class="bg-gray-50 dark:bg-gray-800/50">
                            <TableRow class="hover:bg-transparent">
                                <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">ID</TableHead>
                                <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">NOMBRE</TableHead>
                                <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">CORREO</TableHead>
                                <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">USUARIO</TableHead>
                                <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">FECHA</TableHead>
                                <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">ESTADO</TableHead>
                                <TableHead class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">ACCIONES</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <TableRow v-for="user in userList" :key="user.id" class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                <TableCell class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ user.id }}</TableCell>
                                <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ user.name }}</TableCell>
                                <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ user.email }}</TableCell>
                                <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ user.username }}</TableCell>
                                <TableCell class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ user.created_at }}</TableCell>
                                <TableCell class="px-4 py-3">
                                    <span v-if="user.status === true" class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                        <span class="mr-1 h-2 w-2 rounded-full bg-green-500 dark:bg-green-400"></span>Activo
                                    </span>
                                    <span v-else class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 dark:bg-red-900/30 dark:text-red-200">
                                        <span class="mr-1 h-2 w-2 rounded-full bg-red-500 dark:bg-red-400"></span>
                                        Inactivo
                                    </span>
                                </TableCell>
                                <TableCell class="flex justify-end space-x-2 px-4 py-3">
                                    <Button variant="ghost" size="sm" class="h-8 w-8 p-0 text-orange-600 hover:bg-orange-50 hover:text-orange-700 dark:text-orange-400 dark:hover:bg-orange-900/30 dark:hover:text-orange-300" @click="openModal(user.id)" title="Editar usuario">
                                        <UserPen class="h-4 w-4" />
                                        <span class="sr-only">Editar usuario</span>
                                    </Button>
                                    <Button variant="ghost" size="sm" class="h-8 w-8 p-0 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300" @click="openModalDelete(user.id)" title="Eliminar usuario">
                                        <Trash class="h-4 w-4" />
                                        <span class="sr-only">Eliminar usuario</span>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
            <PaginationUser :meta="userPaginate" @page-change="$emit('page-change', $event)" class="mt-6" />
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
import { UserResource } from '../interface/User';
import PaginationUser from '../../../../components/pagination.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
const { toast } = useToast();

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal', id_user: number): void;
    (e: 'open-modal-delete', id_user: number): void;
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
const { userList, userPaginate } = defineProps<{
    userList: UserResource[];
    userPaginate: Pagination;
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
