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
                />
                <EditUser :user-data="principal.userData" :modal="principal.statusModal.update" @emit-close="closeModal" />
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

const { principal, loadingUsers, getUserById, resetUserData } = useUser();

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingUsers(page);
};

const getIdUser = (id: number) => {
    getUserById(id);
};

const closeModal = (open: boolean) => {
    principal.statusModal.update = open;
    resetUserData();
};
</script>
<style lang="css" scoped></style>
