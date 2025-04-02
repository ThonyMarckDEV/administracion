import { Pagination } from '@/interface/paginacion';

export type CustomerResource = {
    id: number;
    name: string;
    codigo: string;
    client_type_id: number;
    client_type: string;
    state: boolean;
    created_at: string;
};

export type CustomerRequest = {
    name: string;
    codigo: string;
    client_type_id: number;
};

export type CustomerRequestUpdate = {
    name: string;
    codigo: string;
    client_type_id: number;
    state: 'activo' | 'inactivo';
};

export type showCustomerResponse = {
    status: boolean;
    message: string;
    customer: CustomerResource;
};

export type CustomerDeleteResponse = {
    status: boolean;
    message: string;
};

export type CustomerResponse = {
    customers: CustomerResource[];
    pagination: Pagination;
};
