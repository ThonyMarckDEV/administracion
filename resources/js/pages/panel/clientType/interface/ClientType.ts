import { Pagination } from "@/interface/paginacion";

export type ClientTypeResource = {
    id: number;
    name: string;
    state: boolean;
    created_at: string;
};

export type ClientTypeRequest = {
    name: string;
    state: boolean;
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
    state: boolean;
};

export type ClientTypeResponse = {
    clientTypes: ClientTypeResource[];
    pagination: Pagination;
};