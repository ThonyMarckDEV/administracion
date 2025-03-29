<template>
    <div class="container mx-auto px-0">
        <LoadingTable v-if="loading" :headers="7" :row-count="15" />
        <Table v-else class="my-3 w-full overflow-clip rounded-lg border border-gray-100">
            <TableCaption>{{ userPaginate.current_page }} de {{ userPaginate.total }}</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-center">ID</TableHead>
                    <TableHead class="w-[250px]">Nombre</TableHead>
                    <TableHead>Correo</TableHead>
                    <TableHead>Usuario</TableHead>
                    <TableHead>Fecha de creación</TableHead>
                    <TableHead>Estado</TableHead>
                    <TableHead class="text-center">Acciones</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody class="cursor-pointer">
                <TableRow v-for="user in userList" :key="user.id">
                    <td class="text-center font-bold">{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.created_at }}</td>
                    <td class="w-[100px] text-center">
                        <span v-if="user.status === true" class="rounded-full bg-green-400 px-2 py-1 text-white">Activo</span>
                        <span v-else class="rounded-full bg-red-400 px-2 py-1 text-white">Inactivo</span>
                    </td>
                    <td class="flex justify-center gap-2">
                        <Button variant="outline" class="bg-orange-400 text-white shadow-md hover:bg-orange-600" @click="openModal(user.id)">
                            <UserPen class="h-5 w-5" />
                        </Button>
                        <Button variant="outline" class="bg-red-400 text-white shadow-md hover:bg-red-600" @click="openModalDelete(user.id)">
                            <Trash class="h-5 w-5" />
                        </Button>
                    </td>
                </TableRow>
            </TableBody>
        </Table>
        <PaginationUser :meta="userPaginate" @page-change="$emit('page-change', $event)" />
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
import PaginationUser from './paginationUser.vue';
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
