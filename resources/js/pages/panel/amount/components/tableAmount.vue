<template>
    <div class="container mx-auto px-4 py-2">
        <LoadingTable v-if="loading" :headers="6" :row-count="10" />
        <div v-else class="space-y-4">
            <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm dark:border-gray-700 dark:shadow-none">
                <Table class="w-full">
                    <TableHeader class="bg-gray-50 dark:bg-gray-800/50">
                        <TableRow class="hover:bg-transparent">
                            <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">ID</TableHead>
                            <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">CATEGORIA</TableHead>
                            <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">PROVEEDOR</TableHead>
                            <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">RUC</TableHead>
                            <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">DESCRIPCION</TableHead>
                            <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">MONTO</TableHead>
                            <TableHead class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">FECHA_INIT</TableHead>
                            <TableHead class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">ACCIONES</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <TableRow v-for="amount in amountsList" :key="amount.id" class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <TableCell class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ amount.id }}</TableCell>
                            <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ amount.category }}</TableCell>
                            <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ amount.supplier }}</TableCell>
                            <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ amount.ruc }}</TableCell>
                            <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ amount.description }}</TableCell>
                            <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ amount.amount }}</TableCell>
                            <TableCell class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ amount.date_init }}</TableCell>
                            <TableCell class="flex justify-end space-x-2 px-4 py-3">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-8 w-8 p-0 text-blue-600 hover:bg-blue-50 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                                    @click="openModalPdf(amount.id)"
                                    title="Ver Recibo"
                                >
                                    <FileText class="h-4 w-4" />
                                    <span class="sr-only">Ver Recibo</span>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-8 w-8 p-0 text-orange-600 hover:bg-orange-50 hover:text-orange-700 dark:text-orange-400 dark:hover:bg-orange-900/30 dark:hover:text-orange-300"
                                    @click="openModalCreate(amount.id)"
                                    title="Editar cliente"
                                >
                                    <UserPen class="h-4 w-4" />
                                    <span class="sr-only">Editar cliente</span>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-8 w-8 p-0 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300"
                                    @click="openModalDelete(amount.id)"
                                    title="Eliminar cliente"
                                >
                                    <Trash class="h-4 w-4" />
                                    <span class="sr-only">Eliminar cliente</span>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <PaginationCategory :meta="amountsPaginate" @page-change="$emit('page-change', $event)" />
            </div>
        </div>
        <ShowPdfModal
            :status-modal="showPdfModal"
            :pdf-url="pdfUrl"
            @close-modal="closePdfModal"
        />
    </div>
</template>
<script setup lang="ts">
import LoadingTable from '@/components/loadingTable.vue';
import { Button } from '@/components/ui/button';
import { Table, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import TableBody from '@/components/ui/table/TableBody.vue';
import { useToast } from '@/components/ui/toast';
import { Pagination } from '@/interface/paginacion';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { FileText, Trash, UserPen } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import PaginationCategory from '../../category/components/paginationCategory.vue';
import ShowPdfModal from './ShowPdfModal.vue';
import { AmountResource } from '../interface/Amount';
import { AmountServices } from '@/services/amountServices';

const { toast } = useToast();

const emit = defineEmits<{
    (e: 'page-change', page: number): void;
    (e: 'open-modal-create', id_mount: number): void;
    (e: 'open-modal-delete', id_mount: number): void;
}>();

const { amountsList, amountsPaginate, loading } = defineProps<{
    amountsList: AmountResource[];
    amountsPaginate: Pagination;
    loading: boolean;
}>();

const page = usePage<SharedData>();

const message = ref(page.props.flash.message || '');
const showPdfModal = ref(false);
const pdfUrl = ref<string | null>(null);

const openModalCreate = (id: number) => {
    emit('open-modal-create', id);
};

const openModalDelete = (id: number) => {
    emit('open-modal-delete', id);
};

const openModalPdf = async (id: number) => {
    try {
        const response = await AmountServices.generatePdf(id);
        if (response.status) {
            const pdfBlob = base64ToBlob(response.pdf, 'application/pdf');
            pdfUrl.value = URL.createObjectURL(pdfBlob);
            showPdfModal.value = true;
        } else {
            toast({
                title: 'Error',
                description: response.message,
                variant: 'destructive',
            });
        }
    } catch (error) {
        console.error('Error al generar el PDF:', error);
        toast({
            title: 'Error',
            description: 'No se pudo generar el PDF.',
            variant: 'destructive',
        });
    }
};

const closePdfModal = () => {
    if (pdfUrl.value) {
        URL.revokeObjectURL(pdfUrl.value);
        pdfUrl.value = null;
    }
    showPdfModal.value = false;
};

const base64ToBlob = (base64: string, mimeType: string) => {
    const byteCharacters = atob(base64);
    const byteNumbers = new Array(byteCharacters.length);
    for (let i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    const byteArray = new Uint8Array(byteNumbers);
    return new Blob([byteArray], { type: mimeType });
};

onMounted(() => {
    if (message.value) {
        toast({
            title: 'Notificación',
            description: message.value,
        });
    }
});
</script>
<style scoped>
/* Mejoras específicas para modo oscuro */
.dark .TableHeader {
    background-color: rgba(31, 41, 55, 0.5);
    border-bottom-color: rgba(55, 65, 81, 0.5);
}

/* Transiciones mejoradas */
.TableRow {
    transition:
        background-color 0.15s ease,
        transform 0.1s ease;
}

.TableRow:hover {
    transform: translateY(-1px);
}

/* Estilo para la tabla vacía */
.TableBody:empty::after {
    content: 'No se encontraron clientes';
    display: block;
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.dark .TableBody:empty::after {
    color: #9ca3af;
}
</style>
