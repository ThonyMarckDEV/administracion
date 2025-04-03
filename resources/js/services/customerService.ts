import { InputClientTypeResponse } from '@/interface/Inputs';
import {
    CustomerDeleteResponse,
    CustomerRequest,
    CustomerRequestUpdate,
    CustomerResponse,
    showCustomerResponse,
} from '@/pages/panel/customer/interface/Customer';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export const CustomerServices = {
    // list customers
    async index(page: number, name: string): Promise<CustomerResponse> {
        const response = await axios.get(`/panel/listar-customers?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    // inertia
    async store(data: CustomerRequest) {
        router.post(route('panel.customers.store'), data);
    },
    // show customer
    async show(id: number): Promise<showCustomerResponse> {
        const response = await axios.get(`/panel/customers/${id}`);
        return response.data;
    },
    // update customer
    async update(id: number, data: CustomerRequestUpdate): Promise<showCustomerResponse> {
        const response = await axios.put(`/panel/customers/${id}`, data);
        return response.data;
    },
    async destroy(id: number): Promise<CustomerDeleteResponse> {
        return await axios.delete(`/panel/customers/${id}`);
    },
    // get client_type
    async getClientType(): Promise<InputClientTypeResponse> {
        return await axios.get('/panel/inputs/client_type_list');
    },
};
