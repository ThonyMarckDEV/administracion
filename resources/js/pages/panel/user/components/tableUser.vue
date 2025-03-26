<template>
     <div class="container mx-auto py-6 px-0">
            <Table class="w-full border border-gray-100 rounded-lg overflow-clip">
                <TableCaption>lista de usuarios</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="text-center">ID</TableHead>    
                        <TableHead class="w-[100px]">Nombre</TableHead>
                        <TableHead>Correo</TableHead>
                        <TableHead>Usuario</TableHead>
                        <TableHead>Fecha de creación</TableHead>
                        <TableHead>Estado</TableHead>
                        <TableHead class="text-center">Acciones</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in userList" :key="user.id">
                        <td class="text-center font-bold">{{ user.id }}</td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.username }}</td>
                        <td>{{ user.created_at }}</td>
                        <td class="w-[100px] text-center">
                            <span v-if="user.status === true" class="bg-green-400 text-white px-2 py-1 rounded-full">Activo</span>
                            <span v-else class="bg-red-400 text-white px-2 py-1 rounded-full">Inactivo</span>
                        </td>
                        <td class="flex gap-2 justify-center">
                            <Button variant="outline">
                                <UserPen class="w-4 h-4" />
                            </Button>
                            <Button variant="link">
                                <Trash class="w-4 h-4" />
                            </Button>
                        </td>
                    </TableRow>
                </TableBody>
            </Table>
            <PaginationUser :meta="userPaginate" @page-change="$emit('page-change', $event)"/>
    </div>
</template>
<script setup lang="ts">
import { Pagination } from '@/interface/paginacion';
import { UserResource } from '../interface/User';
import Table from '@/components/ui/table/Table.vue';
import TableCaption from '@/components/ui/table/TableCaption.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import Button from '@/components/ui/button/Button.vue';
import PaginationUser from './paginationUser.vue';
import { Trash, UserPen } from 'lucide-vue-next';
const {userList,userPaginate} = defineProps<{
    userList: UserResource[];
    userPaginate: Pagination;
}>();


</script>
<style scoped lang="css"></style>