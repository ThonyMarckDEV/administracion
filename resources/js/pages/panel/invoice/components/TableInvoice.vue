<template>
    <div class="container mx-auto px-4">
        <LoadingTable v-if="loading" :headers="9" :row-count="10" />
        <div v-else class="space-y-4">
            <div class="overflow-auto rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
                <Table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <TableHeader class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/70">
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">ID</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Tipo</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Serie</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Correlativo</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Cliente</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Servicio</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Monto</TableHead>
                        <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">SUNAT</TableHead>
                        <TableHead class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Acciones</TableHead>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="invoice in invoiceList"
                            :key="invoice.id"
                            class="transition-colors duration-150 ease-in-out hover:bg-gray-50 dark:hover:bg-gray-800/30"
                        >
                            <TableCell class="border-b border-gray-100 px-4 py-3 font-semibold text-gray-900 dark:border-gray-700 dark:text-gray-100">
                                {{ invoice.id }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ invoice.document_type }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ invoice.serie_assigned }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ invoice.correlative_assigned }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ invoice.payment.customer?.name ?? 'N/A' }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                {{ invoice.payment.service?.name ?? 'N/A' }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 font-mono text-green-600 dark:border-gray-700 dark:text-green-400">
                                {{ invoice.payment.amount }}
                            </TableCell>
                            <TableCell class="border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': invoice.sunat === 'enviado',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': invoice.sunat === 'anulado',
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300': invoice.sunat === 'Pendiente',
                                    }"
                                    class="rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ invoice.sunat }}
                                </span>
                            </TableCell>
                            <TableCell class="flex justify-end space-x-2 border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-[30px] w-[30px] rounded-md p-0 text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/30"
                                    @click="openModalShow(invoice.id)"
                                    title="Ver Detalles"
                                >
                                    <Eye class="h-[16px] w-[16px]" />
                                    <span class="sr-only">Ver Detalles</span>
                                </Button>
                                <Button
                                    v-if="invoice.sunat !== 'anulado'"
                                    variant="ghost"
                                    size="sm"
                                    class="h-[30px] w-[30px] rounded-md p-0 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30"
                                    @click="openModalAnnul(invoice.id)"
                                    title="Anular Comprobante"
                                >
                                    <XCircle class="h-[16px] w-[16px]" />
                                    <span class="sr-only">Anular Comprobante</span>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <PaginationPayment :meta="invoicePaginate" @page-change="$emit('page-change', $event)" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination } from '@/interface/paginacion';
import { Eye, XCircle } from 'lucide-vue-next';
import PaginationPayment from '../../category/components/paginationCategory.vue';
import { InvoiceResource } from '../interface/Invoice';

defineProps<{
    invoiceList: InvoiceResource[];
    invoicePaginate: Pagination;
    loading: boolean;
}>();

defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal-show', id: number): void;
    (e: 'open-modal-annul', id: number): void;
}>();
</script>