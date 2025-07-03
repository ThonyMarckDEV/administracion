import { InvoiceTable, ShowInvoice, AnnulInvoiceResponse } from '@/pages/panel/invoice/interface/Invoice';
import axios from 'axios';

export const InvoiceServices = {
    async indexInvoices(page: number, search: string): Promise<InvoiceTable> {
        const response = await axios.get(`/panel/listar-invoices?page=${page}&search=${encodeURIComponent(search)}`);
        return response.data;
    },

    async showInvoice(id: number): Promise<ShowInvoice> {
        const response = await axios.get(`/panel/invoices/${id}`);
        return response.data;
    },

    async annulInvoice(id: number, data: { invoice_id: number; motivo: string }): Promise<AnnulInvoiceResponse> {
        const response = await axios.post(`/panel/invoices/${id}/annul`, data);
        return response.data;
    },

    async viewPdf(invoiceId: number, paymentId: number): Promise<Blob> {
        const response = await axios.get(`/panel/invoices/${invoiceId}/pdf/${paymentId}`, {
            responseType: 'blob',
        });
        return response.data;
    },

    async downloadXml(invoiceId: number, paymentId: number, sunatStatus: string): Promise<Blob> {
        const endpoint = sunatStatus === 'anulado' 
            ? `/panel/invoices/${invoiceId}/voided/xml` 
            : `/panel/invoices/${invoiceId}/xml/${paymentId}`;
        const response = await axios.get(endpoint, {
            responseType: 'blob',
        });
        return response.data;
    },

    async downloadCdr(invoiceId: number, paymentId: number, sunatStatus: string): Promise<Blob> {
        const endpoint = sunatStatus === 'anulado' 
            ? `/panel/invoices/${invoiceId}/voided/cdr` 
            : `/panel/invoices/${invoiceId}/cdr/${paymentId}`;
        const response = await axios.get(endpoint, {
            responseType: 'blob',
        });
        return response.data;
    },
};