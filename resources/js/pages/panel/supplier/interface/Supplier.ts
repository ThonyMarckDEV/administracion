import { Pagination } from "@/interface/paginacion";

export type SupplierResource = {
  id: number;
  name: string;
  ruc: string;
  email: string;
  address: string | null;
  state: boolean;
};

export type SupplierRequest = {
  name: string;
  ruc: string;
  email: string;
  address: string | null;
};

export type SupplierUpdateRequest = {
  name: string;
  ruc: string;
  email: string;
  address: string | null;
  state: 'activo' | 'inactivo';
};

export type showSupplierResponse = {
  state: boolean;
  message: string;
  supplier: SupplierResource;
};

export type SupplierDeleteResponse = {
  state: boolean;
  message: string;
};

export type SupplierResponse = {
  suppliers: SupplierResource[];
  pagination: Pagination;
};