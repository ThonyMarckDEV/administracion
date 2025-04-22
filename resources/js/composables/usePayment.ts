import { Pagination } from '@/interface/paginacion';
import { PaymentResource } from '@/pages/panel/payment/interface/Payment';
import { PaymentServices } from '@/services/paymentServices';
import { reactive } from 'vue';

export const usePayment = () => {
    const principal = reactive<{
        paymentList: PaymentResource[];
        paginacion: Pagination;
        loading: boolean;
    }>({
        paymentList: [],
        paginacion: {
            total: 0,
            current_page: 0,
            per_page: 0,
            last_page: 0,
            from: 0,
            to: 0,
        },
        loading: false,
    });

    const loadingPayments = async (page: number = 1, customer: string = '') => {
        try {
            principal.loading = true;
            const response = await PaymentServices.index(page, customer);
            principal.paymentList = response.payments;
            principal.paginacion = response.pagination;
        } catch (error) {
            console.error('Error loading payments:', error);
        } finally {
            principal.loading = false;
        }
    };

    return {
        principal,
        loadingPayments,
    };
};
