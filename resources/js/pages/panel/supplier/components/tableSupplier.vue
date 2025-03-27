<template>
    <div class="container mx-auto py-6 px-0">
           <Table class="w-full border border-gray-100 rounded-lg overflow-clip">
               <TableCaption>Lista de proveedores</TableCaption>
               <TableHeader>
                   <TableRow>
                       <TableHead class="text-center">ID</TableHead>    
                       <TableHead class="w-[100px]">Nombre</TableHead>
                       <TableHead class="text-left px-10">Ruc</TableHead>
                       <TableHead class="text-left px-1">Dirección</TableHead>
                       <TableHead>Estado</TableHead>
                       <TableHead class="text-center">Acciones</TableHead>
                   </TableRow>
               </TableHeader>
               <TableBody>
                   <TableRow v-for="supplier in supplierList" :key="supplier.id">
                       <td class="text-center font-bold">{{ supplier.id }}</td>
                       <td class="text-left px-2">{{ supplier.name }}</td>
                       <td class="text-left px-10">{{ supplier.ruc }}</td>
                       <td>{{ supplier.address }}</td>
                       <td class="w-[100px] text-center">
                           <span v-if="supplier.state === true" class="bg-green-400 text-white px-2 py-1 rounded-full">Activo</span>
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
           <PaginationSupplier :meta="supplierPaginate" @page-change="$emit('page-change', $event)"/>
   </div>
</template>
<script setup lang="ts">
import { Pagination } from '@/interface/paginacion';
import { SupplierResource } from '../interface/Supplier';
import Table from '@/components/ui/table/Table.vue';
import TableCaption from '@/components/ui/table/TableCaption.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import Button from '@/components/ui/button/Button.vue';
import PaginationSupplier from './paginationSupplier.vue';
import { Trash, UserPen } from 'lucide-vue-next';
const {supplierList,supplierPaginate} = defineProps<{
   supplierList: SupplierResource[];
   supplierPaginate: Pagination;
}>();

console.log(supplierPaginate); 

</script>
<style scoped lang="css"></style>