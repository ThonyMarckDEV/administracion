<!-- TableInvoice.vue -->
<template>
  <div class="container mx-auto px-4">
    <FilterInvoices @filter="applyFilters" />
    <LoadingTable v-if="loading" :headers="12" :row-count="10" />
    <div v-else class="space-y-4">
      <div class="overflow-auto rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
        <Table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
          <TableHeader class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/70">
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">ID</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">ID Pago</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Tipo</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Serie</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Correlativo</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Cliente</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Servicio</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Monto</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Fecha de Pago</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">Método de Pago</TableHead>
            <TableHead class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-300">SUNAT</TableHead>
            <TableHead class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Acciones</TableHead>
          </TableHeader>
          <TableBody>
            <TableRow
              v-for="invoice in filteredInvoices"
              :key="invoice.id"
              class="transition-colors duration-150 ease-in-out hover:bg-gray-50 dark:hover:bg-gray-800/30"
            >
              <TableCell class="border-b border-gray-100 px-4 py-3 font-semibold text-gray-900 dark:border-gray-700 dark:text-gray-100">
                {{ invoice.id }}
              </TableCell>
              <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                {{ invoice.payment_id }}
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
              <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                {{ invoice.payment.payment_date ? new Date(invoice.payment.payment_date).toLocaleDateString('es-PE') : 'N/A' }}
              </TableCell>
              <TableCell class="border-b border-gray-100 px-4 py-3 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                {{ invoice.payment.payment_method }}
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
                  @click="openPdfModal(invoice.id, invoice.payment_id)"
                  title="Ver PDF"
                >
                  <FileText class="h-[16px] w-[16px]" />
                  <span class="sr-only">Ver PDF</span>
                </Button>
                <Button
                  variant="ghost"
                  size="sm"
                  class="h-[30px] w-[30px] rounded-md p-0 text-green-500 hover:bg-green-100 dark:hover:bg-green-900/30"
                  @click="downloadXml(invoice.id, invoice.payment_id, invoice.sunat)"
                  title="Descargar XML"
                >
                  <FileCode class="h-[16px] w-[16px]" />
                  <span class="sr-only">Descargar XML</span>
                </Button>
                <Button
                  variant="ghost"
                  size="sm"
                  class="h-[30px] w-[30px] rounded-md p-0 text-purple-500 hover:bg-purple-100 dark:hover:bg-purple-900/30"
                  @click="downloadCdr(invoice.id, invoice.payment_id, invoice.sunat)"
                  title="Descargar CDR"
                >
                  <FileArchive class="h-[16px] w-[16px]" />
                  <span class="sr-only">Descargar CDR</span>
                </Button>
                <Button
                  v-if="invoice.sunat !== 'anulado' && invoice.document_type.toLowerCase() !== 'boleta'"
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

    <ShowPdfModal
      :status-modal="showPdfModal"
      :pdf-url="pdfUrl"
      @close-modal="closePdfModal"
    />
    <AnnulInvoiceModal
      :status-modal="showAnnulModal"
      :invoice-id="selectedInvoiceId"
      @close-modal="closeModalAnnul"
      @annul-success="handleAnnulSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import LoadingTable from '@/components/loadingTable.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination } from '@/interface/paginacion';
import { FileText, FileCode, FileArchive, XCircle } from 'lucide-vue-next';
import PaginationPayment from '../../category/components/paginationCategory.vue';
import FilterInvoices from './FilterInvoices.vue';
import ShowPdfModal from './ShowPdfModal.vue';
import AnnulInvoiceModal from './AnnulInvoiceModal.vue';
import { InvoiceResource } from '../interface/Invoice';
import { InvoiceServices } from '@/services/invoiceServices';

// Destructure props
const { invoiceList, invoicePaginate, loading } = defineProps<{
  invoiceList: InvoiceResource[];
  invoicePaginate: Pagination;
  loading: boolean;
}>();

const emit = defineEmits<{
  (e: 'page-change', page: number): void;
  (e: 'refresh-list'): void;
}>();

const showPdfModal = ref(false);
const pdfUrl = ref<string | null>(null);
const showAnnulModal = ref(false);
const selectedInvoiceId = ref<number>(0);

const filters = ref({
  document_type: '',
  payment_id: '',
  correlative_assigned: '',
});

const filteredInvoices = computed(() => {
  return invoiceList.filter((invoice) => {
    const matchesDocumentType =
      !filters.value.document_type ||
      invoice.document_type.toLowerCase() === filters.value.document_type.toLowerCase();
    const matchesPaymentId =
      !filters.value.payment_id ||
      invoice.payment_id.toString().includes(filters.value.payment_id);
    const matchesCorrelative =
      !filters.value.correlative_assigned ||
      invoice.correlative_assigned
        .toLowerCase()
        .includes(filters.value.correlative_assigned.toLowerCase());

    return matchesDocumentType && matchesPaymentId && matchesCorrelative;
  });
});

const applyFilters = (newFilters: { document_type: string; payment_id: string; correlative_assigned: string }) => {
  filters.value = { ...newFilters };
};

const openPdfModal = async (invoiceId: number, paymentId: number) => {
  try {
    const blob = await InvoiceServices.viewPdf(invoiceId, paymentId);
    pdfUrl.value = URL.createObjectURL(blob);
    showPdfModal.value = true;
  } catch (error) {
    console.error('Error al cargar el PDF:', error);
    alert('No se pudo cargar el PDF.');
  }
};

const closePdfModal = () => {
  if (pdfUrl.value) {
    URL.revokeObjectURL(pdfUrl.value);
    pdfUrl.value = null;
  }
  showPdfModal.value = false;
};

const downloadXml = async (invoiceId: number, paymentId: number, sunatStatus: string) => {
  try {
    const blob = await InvoiceServices.downloadXml(invoiceId, paymentId, sunatStatus);
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = sunatStatus === 'anulado' ? `voided-${invoiceId}.xml` : `invoice-${invoiceId}.xml`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Error al descargar el XML:', error);
    alert('No se pudo descargar el XML.');
  }
};

const downloadCdr = async (invoiceId: number, paymentId: number, sunatStatus: string) => {
  try {
    const blob = await InvoiceServices.downloadCdr(invoiceId, paymentId, sunatStatus);
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = sunatStatus === 'anulado' ? `voided-cdr-${invoiceId}.zip` : `cdr-${invoiceId}.zip`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  } catch (error) {
    console.error('Error al descargar el CDR:', error);
    alert('No se pudo descargar el CDR.');
  }
};

const openModalAnnul = (invoiceId: number) => {
  selectedInvoiceId.value = invoiceId;
  showAnnulModal.value = true;
};

const closeModalAnnul = () => {
  console.log('Closing annul modal');
  showAnnulModal.value = false;
  selectedInvoiceId.value = 0;
};

const handleAnnulSuccess = () => {
  console.log('Annul success, emitting refresh-list');
  emit('refresh-list');
};
</script>