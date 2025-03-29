import { Pagination } from '@/interface/paginacion';

export type ServiceResource = {
    id: number;
    name: string;
    cost: number;
    ini_date: string;
    state: boolean;
    created_at: string;
};

export type ServiceRequest = {
    name: string;
    cost: number;
    ini_date: string;
    password?: string;
    state: 'activo' | 'inactivo';
};

export type ShowServiceResponse = {
    status: boolean;
    message: string;
    service: ServiceResource;
};

export type ServiceDeleteResponse = {
    status: boolean;
    message: string;
};

export type ServiceUpdateRequest = {
    name: string;
    cost: number;
    ini_date: string;
    state: 'activo' | 'inactivo';
};

export type ServiceResponse = {
    services: ServiceResource[];
    pagination: Pagination;
};