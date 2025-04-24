import { InputCustomerResponse, InputServiceResponse } from '@/interface/Inputs';
import { PaymentTable, showPayment } from '@/pages/panel/payment/interface/Payment';
import axios from 'axios';

export const PaymentServices = {
    // automcomplete
    async getCustomers(texto: string): Promise<InputCustomerResponse> {
        return await axios.get(`/panel/inputs/customers_list?texto=${encodeURIComponent(texto)}`);
    },
    async getServices(texto: string): Promise<InputServiceResponse> {
        return await axios.get(`/panel/inputs/services_list?texto=${encodeURIComponent(texto)}`);
    },
    async index(page: number, customer: string): Promise<PaymentTable> {
        const response = await axios.get(`/panel/listar-payments?page=${page}&customer=${encodeURIComponent(customer)}`);
        return response.data;
    },
    async show(id: number): Promise<showPayment> {
        const response = await axios.get(`/panel/payments/${id}`);
        return response.data;
    },
};
