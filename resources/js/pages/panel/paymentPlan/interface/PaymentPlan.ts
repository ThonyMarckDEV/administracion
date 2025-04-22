import { Pagination } from '@/interface/paginacion';

export type PaymentPlanResource = {
    id: number;
    service_id: number;
    service_name: string;
    period_id: number;
    period_name: string;
    payment_type: boolean;
    amount: number;
    duration: number;
    state: boolean;
};

export type PaymentPlanRequest = {
    service_id: number;
    period_id: number;
    payment_type: 'anual' | 'mensual';
    amount: number;
    duration: number;
};

export type PaymentPlanRequestUpdate = {
    service_id: number;
    period_id: number;
    payment_type: 'anual' | 'mensual';
    amount: number;
    duration: number;
    state: 'activo' | 'inactivo';
};

export type showPaymentPlanResponse = {
    status: boolean;
    message: string;
    paymentPlan: PaymentPlanResource;
};

export type PaymentPlanDeleteResponse = {
    status: boolean;
    message: string;
};

export type PaymentPlanResponse = {
    paymentPlans: PaymentPlanResource[];
    pagination: Pagination;
};
