import { InputService, InputPeriod, InputCustomer } from '@/interface/Inputs';
import { Pagination } from '@/interface/paginacion';
import { PaymentPlanResource, PaymentPlanRequest, PaymentPlanRequestUpdate } from '@/pages/panel/paymentPlan/interface/PaymentPlan';
import { PaymentPlanServices } from '@/services/paymentPlanServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';

export const usePaymentPlan = () => {
    const principal = reactive<{
        paymentPlanList: PaymentPlanResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idPaymentPlan: number;
        stateModal: {
            update: boolean;
            delete: boolean;
        };
        paymentPlanData: PaymentPlanResource;
        serviceList: InputService[];
        periodList: InputPeriod[];
        customerList: InputCustomer[];
    }>({
        paymentPlanList: [],
        paginacion: {
            total: 0,
            current_page: 0,
            per_page: 0,
            last_page: 0,
            from: 0,
            to: 0,
        },
        loading: false,
        filter: '',
        idPaymentPlan: 0,
        stateModal: {
            update: false,
            delete: false,
        },
        paymentPlanData: {
            id: 0,
            service_id: 0,
            service_name: '',
            customer_id: 0,
            customer_name: '',
            period_id: 0,
            period_name: '',
            payment_type: true,
            amount: 0,
            duration: 0,
            state: true,
        },
        serviceList: [],
        periodList: [],
        customerList: [],
    });

    // reset paymentPlan data
    const resetPaymentPlanData = () => {
        principal.paymentPlanData = {
            id: 0,
            service_id: 0,
            service_name: '',
            customer_id: 0,
            customer_name: '',
            period_id: 0,
            period_name: '',
            payment_type: true,
            amount: 0,
            duration: 0,
            state: true,
        }
    }
    // loading payment Plans
    const loadingPaymentPlan = async (page: number = 1, name: string = '') => {
            principal.loading = true;
            try {
                const response = await PaymentPlanServices.index(page, name);
                principal.paymentPlanList = response.paymentPlans;
                principal.paginacion = response.pagination;
                console.log(response);
                const [periodResponse, serviceResponse, customerResponse] = await Promise.all([
                    PaymentPlanServices.getPeriod(),
                    PaymentPlanServices.getService(),
                    PaymentPlanServices.getCustomer(),
                ]);
                principal.periodList = periodResponse.data;
                principal.serviceList = serviceResponse.data;
                principal.customerList = customerResponse.data;
            } catch (error) {
                console.error(error);
            } finally {
                principal.loading = false;
            }
    };

    // creating payment Plan
    const createPaymentPlan = async (data: PaymentPlanRequest) => {
        try {
            await PaymentPlanServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };

    // get product by id
    const getPaymentPlanById = async (id: number) => {
        try {
            if (id === 0) {
                resetPaymentPlanData();
                return;
            }
            const response = await PaymentPlanServices.show(id);
            if (response.status) {
                principal.paymentPlanData = response.paymentPlan;
                principal.idPaymentPlan = response.paymentPlan.id;
                if (principal.serviceList.length === 0) {
                    const serviceResponse = await PaymentPlanServices.getService();
                    principal.serviceList = serviceResponse.data;
                    console.log('envie la peticion');
                }
                if (principal.customerList.length === 0) {
                    const customerResponse = await PaymentPlanServices.getCustomer();
                    principal.customerList = customerResponse.data;
                    console.log('envie la peticion');
                }
                if (principal.periodList.length === 0) {
                    const periodResponse = await PaymentPlanServices.getPeriod();
                    principal.periodList = periodResponse.data;
                    console.log('envie la peticion');
                }
                principal.stateModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    // update product
    const updatePaymentPlan = async (id: number, data: PaymentPlanRequestUpdate) => {
        try {
            const response = await PaymentPlanServices.update(id, data);
            if (response.status) {
                showSuccessMessage('Plan de pago actualizado', 'El plan de pago se actualizó correctamente');
                principal.stateModal.update = false;
                loadingPaymentPlan(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.serviceList = [];
            principal.customerList = [];
            principal.periodList = [];
        }
    };

    //delete paymentPlan
    const deletePaymentPlan = async (id: number) => {
        try {
            const response = await PaymentPlanServices.destroy(id);
            if (response.status) {
                showSuccessMessage('Plan de pago eliminado', 'El plan de pago se elimino correctamente');
                principal.stateModal.delete = false;
                loadingPaymentPlan(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.stateModal.delete = false;
        }
    };

    return {
        principal,
        loadingPaymentPlan,
        createPaymentPlan,
        getPaymentPlanById,
        resetPaymentPlanData,
        updatePaymentPlan,
        deletePaymentPlan,
    };
};