import { InputClientType } from '@/interface/Inputs';
import { Pagination } from '@/interface/paginacion';
import { CustomerRequest, CustomerRequestUpdate, CustomerResource } from '@/pages/panel/customer/interface/Customer';
import { CustomerServices } from '@/services/customerService';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';

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
            client_type_id: 0,
            client_type: '',
            state: true,
            created_at: '',
        },
        clientTypeList: [],
    });

    // reset customer data
    const resetCustomerData = () => {
        principal.customerData = {
            id: 0,
            name: '',
            codigo: '',
            client_type_id: 0,
            client_type: '',
            state: true,
            created_at: '',
        };
    };

    // loading customers
    const loadingCustomers = async (page: number = 1, name: string = '') => {
        principal.loading = true;
        try {
            const response = await CustomerServices.index(page, name);
            principal.customerList = response.customers;
            principal.paginacion = response.pagination;
            console.log(response);
            if (principal.clientTypeList.length === 0 && principal.paginacion.current_page === 1) {
                const clientTypeResponse = await CustomerServices.getClientType();
                principal.clientTypeList = clientTypeResponse.data;
                console.log('envie la peticion');
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.loading = false;
        }
    };

    // creating customer
    const createCustomer = async (data: CustomerRequest) => {
        try {
            await CustomerServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };

    // get customer by id
    const getCustomerById = async (id: number) => {
        try {
            if (id === 0) {
                resetCustomerData();
                return;
            }
            const response = await CustomerServices.show(id);
            if (response.status) {
                principal.customerData = response.customer;
                console.log(principal.customerData.name);
                principal.idCustomer = response.customer.id;
                if (principal.clientTypeList.length === 0) {
                    const clientTypeResponse = await CustomerServices.getClientType();
                    principal.clientTypeList = clientTypeResponse.data;
                    console.log('envie la peticion');
                }
                principal.statusModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    // update customer
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

    // delete customer
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
