import { Pagination } from "@/interface/paginacion";

export type PeriodResource = {
    id: number;
    name: string;
    description: string;
    state: boolean;
};

export type PeriodRequest = {
    name: string;
    description: string;
    state: "activo" | "inactivo";
};

export type showPeriodResponse = {
    state: boolean;
    message: string;
    period: PeriodResource;
};

export type PeriodDeleteResponse = {
    state: boolean;
    message: string;
};

export type PeriodUpdateRequest = {
    name: string;
    description: string;
    state: "activo" | "inactivo";
};

export type PeriodResponse = {
    periods: PeriodResource[];
    pagination: Pagination;
};
