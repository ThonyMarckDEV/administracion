<template>
    <div class="container mx-auto px-4">
        <LoadingTable v-if="loading" :headers="6" :row-count="10" />
        <div v-else class="space-y-4">
            <div class="overflow-auto rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
                <Table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <TableHeader class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/70">
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">ID</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Cliente</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Servicio</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Descuento</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Monto</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Fecha_Pago</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Metodo</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Referencia</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Estado</TableHead>
                        <TableHead class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Acciones</TableHead>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="payment in paymentList"
                            :key="payment.id"
                            class="transition-colors duration-150 ease-in-out hover:bg-gray-50 dark:hover:bg-gray-800/30"
                        >
                            <TableCell class="border-b border-gray-100 px-4 py-3 font-semibold text-gray-900 dark:border-gray-700 dark:text-gray-100">
                                {{ payment.id }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ payment.customer }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ payment.service }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 font-mono text-blue-600 dark:border-gray-700 dark:text-blue-400">
                                {{ payment.discount }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 font-mono text-green-600 dark:border-gray-700 dark:text-green-400">
                                {{ payment.amount }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                {{ payment.payment_date }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ payment.payment_method }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ payment.reference }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': payment.status === 'pagado',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': payment.status === 'pendiente',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': payment.status === 'vencido',
                                    }"
                                    class="rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ payment.status }}
                                </span>
                            </TableCell>
                            <TableCell
                                v-if="payment.status !== 'pagado'"
                                class="flex justify-end space-x-2 border-b border-gray-100 px-4 py-3 dark:border-gray-700"
                            >
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-[30px] w-[30px] rounded-md p-0 text-orange-500 hover:bg-orange-100 dark:hover:bg-orange-900/30"
                                    @click="openModalCreate(payment.id)"
                                    title="Editar Pago"
                                >
                                    <UserPen class="h-[16px] w-[16px]" />
                                    <span class="sr-only">Editar Pago</span>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-[30px] w-[30px] rounded-md p-0 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30"
                                    @click="openModalDelete(payment.id)"
                                    title="Eliminar pago"
                                >
                                    <Trash class="h-[16px] w-[16px]" />
                                    <span class="sr-only">Eliminar pago</span>
                                </Button>
                            </TableCell>
                            <TableCell
                                v-else
                                class="border-b border-gray-100 px-4 py-3 dark:border-gray-700"
                            ></TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <PaginationPayment :meta="paymentPaginate" @page-change="$emit('page-change', $event)" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { showSuccessMessage } from '@/utils/message';
import { usePage } from '@inertiajs/vue3';
import { Trash, UserPen } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import PaginationPayment from '../../category/components/paginationCategory.vue';
import { PaymentResource } from '../interface/Payment';

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal-create', id_mount: number): void;
    (e: 'open-modal-delete', id_mount: number): void;
}>();

const {} = defineProps<{
    paymentList: PaymentResource[];
    paymentPaginate: Pagination;
    loading: boolean;
}>();

const page = usePage<SharedData>();
const message = ref(page.props.flash.message || '');

const openModalCreate = (id: number) => {
    emit('open-modal-create', id);
};

const openModalDelete = (id: number) => {
    emit('open-modal-delete', id);
};

onMounted(() => {
    if (message.value) {
        showSuccessMessage('Notificación', message.value);
    }
});
</script>
