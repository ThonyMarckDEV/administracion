import { Pagination } from '@/interface/paginacion';

export interface CustomerResource {
    id: number;
    name: string;
    codigo: string;
    email: string;
    dni: string | null;
    ruc: string | null;
    client_type_id: number;
    state: boolean;
    created_at: string;
    updated_at: string;
}

export interface ServiceResource {
    id: number;
    name: string;
}

export interface PaymentResource {
    id: number;
    customer: CustomerResource | null;
    service: ServiceResource | null;
    amount: string;
    payment_date: string | null;
    payment_method: string;
    reference: string | null;
    status: string;
}

export interface InvoiceResource {
    id: number;
    payment_id: number;
    document_type: string;
    serie_assigned: string;
    correlative_assigned: string;
    sunat: string | null;
    created_at: string;
    payment: PaymentResource;
}

export interface InvoiceTable {
    invoices: InvoiceResource[];
    pagination: Pagination;
}

export interface ShowInvoice {
    status: boolean;
    message: string;
    invoice: InvoiceResource;
}

export interface AnnulInvoiceResponse {
    status: boolean;
    message: string;
}