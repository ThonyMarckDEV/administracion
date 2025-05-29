import { Pagination } from '@/interface/paginacion';
import { CustomerRequest, CustomerRequestUpdate, CustomerResource } from '@/pages/panel/customer/interface/Customer';
import { CustomerServices } from '@/services/customerService';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';
import { InputClientType } from '@/interface/Inputs';

export const useCustomer = () => {
    const principal = reactive<{
        customerList: CustomerResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idCustomer: number;
        statusModal: {
            update: boolean;
            delete: boolean;
        };
        customerData: CustomerResource;
        clientTypeList: InputClientType[];
    }>({
        customerList: [],
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
        idCustomer: 0,
        statusModal: {
            update: false,
            delete: false,
        },
        customerData: {
            id: 0,
            name: '',
            codigo: '',
            email: '',
            dni: null,
            ruc: null,
            client_type_id: 0,
            client_type: '',
            state: true,
            created_at: '',
        },
        clientTypeList: [],
    });

    // Reset customer data
    const resetCustomerData = () => {
        principal.customerData = {
            id: 0,
            name: '',
            codigo: '',
            email: '',
            dni: null,
            ruc: null,
            client_type_id: 0,
            client_type: '',
            state: true,
            created_at: '',
        };
    };

    // Loading customers
    const loadingCustomers = async (page: number = 1, name: string = '') => {
        principal.loading = true;
        try {
            const response = await CustomerServices.index(page, name);
            principal.customerList = response.customers;
            principal.paginacion = response.pagination;
            if (principal.clientTypeList.length === 0 && principal.paginacion.current_page === 1) {
                const clientTypeResponse = await CustomerServices.getClientType();
                principal.clientTypeList = clientTypeResponse.data;
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.loading = false;
        }
    };

    // Creating customer
    const createCustomer = async (data: CustomerRequest) => {
        try {
            await CustomerServices.store(data);
            showSuccessMessage('Cliente creado', 'El cliente se creó correctamente');
            loadingCustomers(principal.paginacion.current_page, principal.filter);
        } catch (error) {
            console.error(error);
        }
    };

    // Get customer by id
    const getCustomerById = async (id: number) => {
        try {
            if (id === 0) {
                resetCustomerData();
                return;
            }
            const response = await CustomerServices.show(id);
            if (response.status) {
                principal.customerData = response.customer;
                principal.idCustomer = response.customer.id;
                if (principal.clientTypeList.length === 0) {
                    const clientTypeResponse = await CustomerServices.getClientType();
                    principal.clientTypeList = clientTypeResponse.data;
                }
                principal.statusModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    // Update customer
    const updateCustomer = async (id: number, data: CustomerRequestUpdate) => {
        try {
            const response = await CustomerServices.update(id, data);
            if (response.status) {
                showSuccessMessage('Cliente actualizado', response.message);
                principal.statusModal.update = false;
                loadingCustomers(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.clientTypeList = [];
        }
    };

    // Delete customer
    const deleteCustomer = async (id: number) => {
        try {
            const response = await CustomerServices.destroy(id);
            if (response.status) {
                showSuccessMessage('Cliente eliminado', 'El cliente se eliminó correctamente');
                principal.statusModal.delete = false;
                loadingCustomers(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.statusModal.delete = false;
        }
    };

    return {
        principal,
        loadingCustomers,
        createCustomer,
        getCustomerById,
        resetCustomerData,
        updateCustomer,
        deleteCustomer,
    };
};