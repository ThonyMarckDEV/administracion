import { Pagination } from '@/interface/paginacion';

// interface for resource payment in backend
export interface PaymentResource {
    id: number;
    customer: string;
    service: string;
    discount: number;
    amount: number;
    payment_date: string;
    payment_method: string;
    reference: string;
    status: string;
}

export interface PaymentTable {
    payments: PaymentResource[];
    pagination: Pagination;
}

export interface PaymentShowResponse {
    status: boolean;
    message: string;
    payment: PaymentResource;
}

export interface PaymentUpdateRequest {
    customer_id: number;
    payment_plan_id: number;
    discount_id: number;
    amount: number;
    payment_date: string;
    payment_method: string;
    reference: string;
    status: string;
}
