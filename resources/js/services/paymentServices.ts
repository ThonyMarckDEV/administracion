import { PaymentTable } from '@/pages/panel/payment/interface/Payment';
import axios from 'axios';

export const PaymentServices = {
    async index(page: number, customer: string): Promise<PaymentTable> {
        const response = await axios.get(`/panel/listar-payments?page=${page}&customer=${encodeURIComponent(customer)}`);
        return response.data;
    },
};
