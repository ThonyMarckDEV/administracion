<template>
        <Table class="border border-gray-100 rounded-lg overflow-clip">
            <TableCaption>lista de usuarios</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-center">ID</TableHead>    
                    <TableHead class="w-[100px]">Nombre</TableHead>
                    <TableHead>Correo</TableHead>
                    <TableHead>Usuario</TableHead>
                    <TableHead>Estado</TableHead>
                    <TableHead>Fecha de creación</TableHead>
                    <TableHead class="text-center">Acciones</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="user in userList" :key="user.id" class="m-2">
                    <td class="text-center font-bold">{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.username }}</td>
                    <td>{{ user.status }}</td>
                    <td>{{ user.created_at }}</td>
                    <td class="flex gap-2 justify-center">
                        <Button variant="default">Editar</Button>
                        <Button variant="destructive">Eliminar</Button>
                    </td>
                </TableRow>
            </TableBody>
        </Table>
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

const {userList,userPaginate} = defineProps<{
    userList: UserResource[];
    userPaginate: Pagination;
}>();

console.log(userPaginate); 

</script>
<style scoped lang="css"></style>