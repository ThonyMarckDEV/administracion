import { Pagination } from "@/interface/paginacion";

export type UserResource = {
  id: number;
  name: string;
  email: string;
  username: string;
  status: boolean;
  created_at: string;
};

export type UserResponse = {
  users: UserResource[];
  pagination: Pagination;
};