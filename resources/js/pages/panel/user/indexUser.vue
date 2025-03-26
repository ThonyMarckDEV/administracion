<template>
    <Head title="users"></Head>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <TableUser :user-list="principal.userList" :user-paginate="principal.paginacion" @page-change="handlePageChange"/>
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import TableUser from './components/tableUser.vue';
import { useUser } from '@/composables/useUser';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'crear usuario',
        href: '/dashboard',
    },
];


const {principal,loadingUsers} = useUser();

// get pagination
const handlePageChange = (page: number) => {
    console.log(page);
    loadingUsers(page);
}
</script>
<style lang="css" scoped></style>