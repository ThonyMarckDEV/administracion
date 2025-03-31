import { Pagination } from "@/interface/paginacion";

export type DiscountResource = {
  id: number;
  description: string;
  percentage: number;
  state: boolean;
};

export type DiscountRequest = {
  description: string;
  percentage: number;
  state: boolean; // ✅ Tipo correcto
};

export type showDiscountResponse = {
    state: boolean;
    message: string;
    discount: DiscountResource;
};

export type DiscountDeleteResponse = {
    state: boolean;
    message: string;
};

export type DiscountUpdateRequest = {
  description: string;
  percentage: number;
  state: boolean; // ✅ Tipo correcto
};
export type DiscountResponse = {
  discounts: DiscountResource[];
  pagination: Pagination;
};