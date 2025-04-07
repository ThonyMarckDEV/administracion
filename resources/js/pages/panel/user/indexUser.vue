<template>
    <Head title="Usuarios"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex justify-between items-center mb-4 px-6 mt-4">
                    <ToolsUser @import-success="loadingUsers" />
                    <FilterUser @search="searchUser" />
                </div>
                <TableUser
                    :user-list="principal.userList"
                    :user-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal="getIdUser"
                    @open-modal-delete="openDeleteModal"
                    :loading="principal.loading"
                />
                <EditUser
                    :user-data="principal.userData"
                    :modal="principal.statusModal.update"
                    @emit-close="closeModal"
                    @update-user="emitUpdateUser"
                />
                <DeleteUser
                    :modal="principal.statusModal.delete"
                    :itemId="principal.idUser"
                    title="Eliminar Servicio"
                    description="¿Está seguro de que desea eliminar este servicio?"
                    @close-modal="closeModalDelete"
                    @delete-item="emitDeleteUser"
                />
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import { useUser } from '@/composables/useUser';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import DeleteUser from '../../../components/delete.vue';
import EditUser from './components/editUser.vue';
import FilterUser from '../../../components/filter.vue';
import TableUser from './components/tableUser.vue';
import { UserUpdateRequest } from './interface/User';
import ToolsUser from './components/toolsUser.vue';

const breadcrumbs: BreadcrumbItem[] = [
{
        title: 'Crear Usuario',
        href: '/panel/users/create',
    },
    {
        title: 'Usuarios',
        href: '/panel/users',
    },
];

onMounted(() => {
    loadingUsers();
});

const { principal, loadingUsers, getUserById, updateUser, deleteUser } = useUser();

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingUsers(page);
};
// get user by id for edit
const getIdUser = (id: number) => {
    getUserById(id);
};
// close modal
const closeModal = (open: boolean) => {
    principal.statusModal.update = open;
};
// close modal delete
const closeModalDelete = (open: boolean) => {
    principal.statusModal.delete = open;
};

// update user
const emitUpdateUser = (user: UserUpdateRequest, id_user: number) => {
    updateUser(id_user, user);
};

// delete user
const openDeleteModal = (userId: number) => {
    principal.statusModal.delete = true;
    principal.idUser = userId;
    console.log('Eliminar usuario con ID:', userId);
};
// delete user
const emitDeleteUser = (userId: number | string) => {
    deleteUser(Number(userId));
};

// search user
const searchUser = (text: string) => {
    loadingUsers(1, text);
};
</script>
<style lang="css" scoped></style>
