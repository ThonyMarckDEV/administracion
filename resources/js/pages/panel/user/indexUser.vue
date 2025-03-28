<template>
    <Head title="users"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <TableUser
                    :user-list="principal.userList"
                    :user-paginate="principal.paginacion"
                    @page-change="handlePageChange"
                    @open-modal="getIdUser"
                    :loading="principal.loading"
                />
                <EditUser
                    :user-data="principal.userData"
                    :modal="principal.statusModal.update"
                    @emit-close="closeModal"
                    @update-user="emitUpdateUser"
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
import EditUser from './components/editUser.vue';
import TableUser from './components/tableUser.vue';
import { UserUpdateRequest } from './interface/User';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'crear usuario',
        href: '/panel/users/create',
    },
    {
        title: 'Exportar',
        href: '/panel/users/export',
    },
    {
        title: 'usuarios',
        href: '/panel/users',
    },
];

onMounted(() => {
    loadingUsers();
});

const { principal, loadingUsers, getUserById, updateUser } = useUser();

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingUsers(page);
};
// get user by id
const getIdUser = (id: number) => {
    getUserById(id);
};
// close modal
const closeModal = (open: boolean) => {
    principal.statusModal.update = open;
};

// update user
const emitUpdateUser = (user: UserUpdateRequest, id_user: number) => {
    updateUser(id_user, user);
};
</script>
<style lang="css" scoped></style>
