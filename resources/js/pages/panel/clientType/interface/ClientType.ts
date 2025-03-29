import { Pagination } from "@/interface/paginacion";

export type ClientTypeResource = {
    id: number;
    name: string;
    state: boolean;
};

export type ClientTypeRequest = {
    name: string;
    state: 'activo' | 'inactivo';
};

export type showClientTypeResponse = {
    state: boolean;
    message: string;
    clientType: ClientTypeResource;
};

export type ClientTypeDeleteResponse = {
    state: boolean;
    message: string;
};

export type ClientTypeUpdateRequest = {
    name: string;
    state: 'activo' | 'inactivo';
};

export type ClientTypeResponse = {
    clientTypes: ClientTypeResource[];
    pagination: Pagination;
};