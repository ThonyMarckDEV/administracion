import { Pagination } from "@/interface/paginacion";

export type ServiceResource = {
  id: number;
  name: string;
  cost: number;
  ini_date: string;
  state: string;
};

export type ServiceResponse = {
  services: ServiceResource[];
  pagination: Pagination;
};