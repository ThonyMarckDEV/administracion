import { Pagination } from "@/interface/paginacion";

export type SupplierResource = {
  id: number;
  name: string;
  ruc: string;
  address: string;
  state: boolean;
};

export type SupplierResponse = {
  suppliers: SupplierResource[];
  pagination: Pagination;
};