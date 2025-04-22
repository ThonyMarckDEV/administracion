import { InputService, InputPeriod, InputServiceResponse, InputPeriodResponse } from '@/interface/Inputs';
import {
    PaymentPlanDeleteResponse,
    PaymentPlanRequest,
    PaymentPlanRequestUpdate,
    PaymentPlanResponse,
    showPaymentPlanResponse,
} from '@/pages/panel/paymentPlan/interface/PaymentPlan';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export const PaymentPlanServices = {
    // list payment plans
    async index(page: number, name: string): Promise<PaymentPlanResponse> {
        const response = await axios.get(`/panel/listar-paymentPlans?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    // inertia
    async store(data: PaymentPlanRequest) {
        router.post(route('panel.paymentPlans.store'), data);
    },
    // show payment plan
    async show(id: number): Promise<showPaymentPlanResponse> {
        const response = await axios.get(`/panel/paymentPlans/${id}`);
        return response.data;
    },
    // udapte payment plan
    async update(id: number, data: PaymentPlanRequestUpdate): Promise<showPaymentPlanResponse> {
        const response = await axios.put(`/panel/paymentPlans/${id}`, data);
        return response.data;
    },

    async destroy(id: number): Promise<PaymentPlanDeleteResponse> {
        return await axios.delete(`/panel/paymentPlans/${id}`);
    },

    //get service
    async getService(): Promise<InputServiceResponse> {
        return await axios.get('/panel/inputs/service_list');
    },

    //get period
    async getPeriod(): Promise<InputPeriodResponse> {
        return await axios.get('/panel/inputs/period_list');
    }
}