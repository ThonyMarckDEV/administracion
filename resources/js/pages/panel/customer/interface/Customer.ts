import { Pagination } from '@/interface/paginacion';

export type CustomerResource = {
    id: number;
    name: string;
    codigo: string;
    email: string;
    dni: string | null;
    ruc: string | null;
    client_type_id: number;
    client_type: string;
    state: boolean;
    created_at: string;
};

export type CustomerRequest = {
    name: string;
    codigo: string;
    email: string;
    dni: string | null;
    ruc: string | null;
    client_type_id: number;
    state: boolean;
};

export type CustomerRequestUpdate = {
    name: string;
    codigo: string;
    email: string;
    dni: string | null;
    ruc: string | null;
    client_type_id: number;
    state: boolean;
};

export type showCustomerResponse = {
    status: boolean;
    message: string;
    customer: CustomerResource;
};

export type CustomerDeleteResponse = {
    state: boolean;
    message: string;
};

export type CustomerResponse = {
    customers: CustomerResource[];
    pagination: Pagination;
};