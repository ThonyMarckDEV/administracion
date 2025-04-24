import { InputCustomer, InputService } from '@/interface/Inputs';
import { Pagination } from '@/interface/paginacion';
import { PaymentResource, updatePayment } from '@/pages/panel/payment/interface/Payment';
import { PaymentServices } from '@/services/paymentServices';
import { reactive, ref } from 'vue';

export const usePayment = () => {
    const principal = reactive<{
        paymentList: PaymentResource[];
        paginacion: Pagination;
        loading: boolean;
        statusModalUpdate: boolean;
        statusModalDelete: boolean;
        payment_id_delete: number;
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
        payment_id_delete: 0,
    });
    const showPaymentData = ref<PaymentResource | null>(null);
    // automcomplete
    const customers = ref<InputCustomer[]>([]);
    const services = ref<InputService[]>([]);

    const getCustomers = async (texto: string = '') => {
        try {
            const response = await PaymentServices.getCustomers(texto);
            customers.value = response.data;
        } catch (error) {
            console.error('Error loading customers:', error);
        }
    };

    const getServices = async (texto: string = '') => {
        try {
            const response = await PaymentServices.getServices(texto);
            services.value = response.data;
        } catch (error) {
            console.error('Error loading services:', error);
        }
    };

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
    const updatePaymentF = async (data: updatePayment, id: number) => {
        try {
            const response = await PaymentServices.update(data, id);
            if (response.status) {
                principal.statusModalUpdate = false;
                showPaymentData.value = null;
                await loadingPayments(principal.paginacion.current_page);
            }
        } catch (error) {
            console.error('Error updating payment:', error);
        }
    };

    const deletePayment = async (id: number) => {
        try {
            const response = await PaymentServices.delete(id);
            if (response.status) {
                principal.statusModalDelete = false;
                await loadingPayments(principal.paginacion.current_page);
            }
        } catch (error) {
            console.error('Error deleting payment:', error);
        }
    };
    return {
        principal,
        showPaymentData,
        customers,
        services,
        getCustomers,
        getServices,
        loadingPayments,
        showPayment,
        updatePaymentF,
        deletePayment,
    };
};
