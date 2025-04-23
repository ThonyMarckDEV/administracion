import { Pagination } from '@/interface/paginacion';
import { PaymentResource } from '@/pages/panel/payment/interface/Payment';
import { PaymentServices } from '@/services/paymentServices';
import { reactive, ref } from 'vue';

export const usePayment = () => {
    const principal = reactive<{
        paymentList: PaymentResource[];
        paginacion: Pagination;
        loading: boolean;
        statusModalUpdate: boolean;
        statusModalDelete: boolean;
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
        statusModalUpdate: false,
        statusModalDelete: false,
    });
    const showPaymentData = ref<PaymentResource | null>(null);
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
    const showPayment = async (id: number) => {
        try {
            const response = await PaymentServices.show(id);
            if (!response.status) {
                showPaymentData.value = null;
                return;
            }
            principal.statusModalUpdate = true;
            showPaymentData.value = response.payment;
        } catch (error) {
            console.error('Error loading payment:', error);
        }
    };
    return {
        principal,
        showPaymentData,
        loadingPayments,
        showPayment,
    };
};
