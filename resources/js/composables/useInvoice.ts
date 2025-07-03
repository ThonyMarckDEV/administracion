import { ref } from 'vue';
import { InvoiceResource, InvoiceTable, ShowInvoice, AnnulInvoiceResponse } from '@/pages/panel/invoice/interface/Invoice';
import { InvoiceServices } from '@/services/invoiceServices';
import { showErrorMessage, showSuccessMessage } from '@/utils/message';

export const useInvoice = () => {
    const principal = ref({
        invoiceList: [] as InvoiceResource[],
        invoicePaginate: {} as any,
        loading: false,
        statusModalShow: false,
        statusModalAnnul: false,
        invoiceIdAnnul: 0,
    });

    const showInvoiceData = ref<InvoiceResource | null>(null);

    const loadInvoices = async (page = 1, search = '') => {
        principal.value.loading = true;
        try {
            const response = await InvoiceServices.indexInvoices(page, search);
            principal.value.invoiceList = response.invoices;
            principal.value.invoicePaginate = response.pagination;
        } catch (error: any) {
            if (error.response?.status === 403) {
                showErrorMessage('Error', 'No tienes permiso para ver los comprobantes.');
            } else {
                showErrorMessage('Error', 'Error al cargar los comprobantes.');
            }
            console.error('Error loading invoices:', error);
        } finally {
            principal.value.loading = false;
        }
    };

    const showInvoice = async (id: number) => {
        principal.value.loading = true;
        try {
            const response = await InvoiceServices.showInvoice(id);
            if (response.status) {
                showInvoiceData.value = response.invoice;
                principal.value.statusModalShow = true;
            }
        } catch (error: any) {
            if (error.response?.status === 403) {
                showErrorMessage('Error', 'No tienes permiso para ver este comprobante.');
            } else {
                showErrorMessage('Error', 'Error al mostrar el comprobante.');
            }
            console.error('Error showing invoice:', error);
        } finally {
            principal.value.loading = false;
        }
    };

  const annulInvoice = async (id: number, data: { invoice_id: number; motivo: string }) => {
    principal.value.loading = true;
    try {
      const response = await InvoiceServices.annulInvoice(id, data);
      if (response.status) {
        showSuccessMessage('Éxito', response.message);
        // Removed loadInvoices call to avoid duplicate refresh
      }
      return response;
    } catch (error: any) {
      if (error.response?.status === 403) {
        showErrorMessage('Error', 'No tienes permiso para anular este comprobante.');
      } else {
        showErrorMessage('Error', error.response?.data?.message || 'Error al anular el comprobante.');
      }
      console.error('Error annulling invoice:', error);
      throw error; // Re-throw to handle in AnnulInvoiceModal
    } finally {
      principal.value.loading = false;
    }
  };

    return { loadInvoices, showInvoice, annulInvoice, principal, showInvoiceData };
};