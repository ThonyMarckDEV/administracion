<template>
    <Head title="users"></Head>
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <TableUser :user-list="principal.userList" :user-paginate="principal.paginacion"/>
            </div>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import TableUser from './components/tableUser.vue';
import { useUser } from '@/composables/useUser';

const {principal, loadingUsers} = useUser();

</script>
<style lang="css" scoped></style>