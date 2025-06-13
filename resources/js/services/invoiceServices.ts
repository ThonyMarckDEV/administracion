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

    async annulInvoice(id: number): Promise<AnnulInvoiceResponse> {
        const response = await axios.post(`/panel/invoices/${id}/annul`);
        return response.data;
    },
};