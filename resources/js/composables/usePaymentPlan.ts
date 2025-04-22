import { InputService, InputPeriod } from '@/interface/Inputs';
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
            period_id: 0,
            period_name: '',
            payment_type: true,
            amount: 0,
            duration: 0,
            state: true,
        },
        serviceList: [],
        periodList: [],
    });

    // reset paymentPlan data
    const resetPaymentPlanData = () => {
        principal.paymentPlanData = {
            id: 0,
            service_id: 0,
            service_name: '',
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
                const [periodResponse, serviceResponse] = await Promise.all([
                    PaymentPlanServices.getPeriod(),
                    PaymentPlanServices.getService(),
                ]);
                principal.periodList = periodResponse.data;
                principal.serviceList = serviceResponse.data;
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

    // get paymentPlan by id
    const getPaymentPlanById = async (id: number) => {
        try {
            if (id === 0) {
                resetPaymentPlanData();
                return;
            }
            const response = await PaymentPlanServices.show(id);
            if (response.status){
                principal.paymentPlanData = response.paymentPlan;
                console.log(principal.paymentPlanData.payment_type);
                principal.idPaymentPlan = response.paymentPlan.id;
                if(principal.paymentPlanList.length === 0){
                    const [periodResponse, serviceResponse] = await Promise.all([
                        PaymentPlanServices.getPeriod(),
                        PaymentPlanServices.getService(),
                      ]);
                    principal.periodList = periodResponse.data;
                    principal.serviceList = serviceResponse.data;
                }
                principal.stateModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    //update paymentPlan
    const updatePaymentPlan = async (id: number, data: PaymentPlanRequestUpdate) => {
        try{
            const response = await PaymentPlanServices.update(id, data);
            if (response.status) {
                showSuccessMessage('Plan de pago actualizado', 'El plan de pago se actualizo correctamente');
                principal.stateModal.update = false;
                loadingPaymentPlan(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally{
            principal.periodList = [];
            principal.serviceList = [];
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